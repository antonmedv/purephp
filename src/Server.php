<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

use React\EventLoop\Factory as LoopFactory;
use React\Socket\Server as SocketServer;
use React\Socket\ConnectionInterface;

class Server implements \ArrayAccess
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
     * @var Command\CommandInterface[]
     */
    private $commands = [];

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
     * @param array $arguments
     * @param ConnectionInterface $connection
     */
    private function runCommand($arguments, ConnectionInterface $connection)
    {
        try {

            $commandClass = array_shift($arguments);

            if (null !== $this->getLogger()) {
                $this->log(
                    'Command from ' . $connection->getRemoteAddress() .
                    ": [$commandClass] " .
                    join(', ', array_map('json_encode', $arguments))
                );
            }


            if (isset($this->commands[$commandClass])) {
                $command = $this->commands[$commandClass];
            } else {

                if (!class_exists($commandClass)) {
                    throw new \RuntimeException("Command class `$commandClass` does not found.");
                }

                $command = new $commandClass($this);

                if (!$command instanceof Command\CommandInterface) {
                    throw new \RuntimeException("Every command must implement Command\\CommandInterface.");
                }

                $this->commands[$commandClass] = $command;
            }

            $result = $command->run($arguments, $connection);
            $result = [self::RESULT, $result];

        } catch (\Exception $e) {

            $result = [self::EXCEPTION, get_class($e), $e->getMessage()];
            $this->log('Exception: ' . $e->getMessage());

        }

        $connection->write(json_encode($result) . self::END_OF_RESULT);
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
     * @return callable
     */
    public function getLogger()
    {
        return $this->logger;
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
     * @param string $name
     * @return Storage\StorageInterface
     */
    public function getStorage($name)
    {
        return $this->storages[$name];
    }

    /**
     * @return \React\EventLoop\LoopInterface
     */
    public function getLoop()
    {
        return $this->loop;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->storages[$offset]);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->storages[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->storages[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->storages[$offset]);
    }
}
