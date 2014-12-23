<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as SocketServer;
use React\Socket\ConnectionInterface;

class Server
{
    const RESULT = 0;

    const EXCEPTION = 1;

    const END_OF_RESULT = 'END_OF_RESULT';

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $host;

    /**
     * @var \Closure
     */
    private $logger;

    /**
     * @var \React\EventLoop\LoopInterface
     */
    private $loop;

    /**
     * @var SocketServer
     */
    private $socket;

    /**
     * @var Storage\StorageInterface[]
     */
    private $storages = [];

    /**
     * @param int $port
     * @param string $host
     */
    public function __construct($port, $host = '127.0.0.1')
    {
        $this->host = $host;
        $this->port = $port;

        $this->loop = LoopFactory::create();

        $this->socket = new SocketServer($this->loop);
        $this->socket->on('connection', array($this, 'onConnection'));
    }

    /**
     * Start event loop.
     */
    public function run()
    {
        $this->log("Server listening on {$this->host}:{$this->port}");
        $this->socket->listen($this->port, $this->host);
        $this->loop->run();
    }

    /**
     * On every new connection add event handler to receive commands from clients.
     *
     * @param ConnectionInterface $connection
     */
    public function onConnection(ConnectionInterface $connection)
    {
        $this->log('New connection from ' . $connection->getRemoteAddress());

        $buffer = '';
        $connection->on('data', function ($data) use (&$buffer, &$connection) {
            $buffer .= $data;

            if (strpos($buffer, Client::END_OF_COMMAND)) {
                $chunks = explode(Client::END_OF_COMMAND, $buffer);
                $count = count($chunks);
                $buffer = $chunks[$count - 1];

                for ($i = 0; $i < $count - 1; $i++) {
                    $command = json_decode($chunks[$i], true);
                    $this->runCommand($command, $connection);
                }
            }
        });
    }

    /**
     * Detect and run command received from client.
     * @param array $command
     * @param ConnectionInterface $connection
     */
    private function runCommand($command, ConnectionInterface $connection)
    {
        try {

            $commandType = array_shift($command);

            switch ($commandType) {

                case Client::STORAGE_COMMAND:
                    $result = $this->runStorageCommand($command, $connection);
                    break;

                case Client::DELETE_COMMAND:
                    $result = $this->runDeleteCommand($command, $connection);
                    break;

                default:
                    throw new \RuntimeException("Unknown command type `$commandType`.");
            }

        } catch (\Exception $e) {

            $result = [self::EXCEPTION, get_class($e), $e->getMessage()];
            $this->log('Exception: ' . $e->getMessage());

        }

        $connection->write(json_encode($result) . self::END_OF_RESULT);
    }


    /**
     * Runs storage command.
     * Command represented as array of next structure [class, name, method, args] where
     *  class - storage full class name,
     *  name - name of storage to store (every storage has to have unique name),
     *  method - method of storage to call,
     *  args - arguments for that method.
     *
     * @param array $command
     * @param ConnectionInterface $connection
     * @return array
     * @throws \RuntimeException
     */
    private function runStorageCommand($command, ConnectionInterface $connection)
    {
        list($class, $name, $method, $args) = $command;

        if (null !== $this->logger) {
            $this->log(
                'Command from ' . $connection->getRemoteAddress() .
                ": [$name] $class::$method(" .
                join(', ', array_map('json_encode', $args)) .
                ')'
            );
        }

        if (isset($this->storages[$name])) {
            if (!$this->storages[$name] instanceof $class) {
                throw new \RuntimeException("Storage `$name` has type `" . get_class($this->storages[$name]) . "` (you request `$class`)");
            }
        } else {
            $this->storages[$name] = new $class();
        }

        $call = [$this->storages[$name], $method];
        $result = call_user_func_array($call, $args);

        return [self::RESULT, $result];
    }

    /**
     * Run delete storage command.
     * 
     * @param array $command
     * @param ConnectionInterface $connection
     * @return array
     */
    private function runDeleteCommand($command, ConnectionInterface $connection)
    {
        list($name) = $command;

        if (isset($this->storages[$name])) {
            unset($this->storages[$name]);
            return [self::RESULT, true];
        } else {
            return [self::RESULT, false];
        }
    }

    /**
     * Before logging massage set logger with `setLogger` method.
     *
     * @param string $message
     */
    public function log($message)
    {
        if (is_callable($this->logger)) {
            $this->logger->__invoke($message);
        }
    }

    /**
     * @param callable $callback
     */
    public function setLogger(\Closure $callback)
    {
        $this->logger = $callback;
    }

    /**
     * @param string $name
     * @return Storage\StorageInterface
     */
    public function getStorage($name)
    {
        return $this->storages[$name];
    }

    /**
     * @param string $name
     * @param mixed|Storage\StorageInterface $storage
     */
    public function setStorage($name, $storage)
    {
        $this->storages[$name] = $storage;
    }

    /**
     * @return \React\EventLoop\LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }
}
