<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

use Pure\Storage\Factory;
use Pure\Storage\LifetimeStorage;
use React\EventLoop\Factory as LoopFactory;
use React\Socket\ConnectionInterface;
use React\Socket\Server as SocketServer;

class Server
{
    const END_OF_RESULT = 'END_OF_RESULT';

    private $port;

    private $host;

    private $logger;

    private $loop;

    private $socket;

    private $stores = [];

    public function __construct($port, $host = '127.0.0.1')
    {
        $this->host = $host;
        $this->port = $port;

        $this->loop = LoopFactory::create();

        $this->socket = new SocketServer($this->loop);
        $this->socket->on('connection', array($this, 'onConnection'));
        $this->loop->addPeriodicTimer(1, array($this, 'onTick'));
    }

    public function run()
    {
        $this->log("Server listening on {$this->host}:{$this->port}");
        $this->socket->listen($this->port, $this->host);
        $this->loop->run();
    }

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

    public function onTick()
    {
        if (isset($this->stores[LifetimeStorage::class])) {
            foreach ($this->stores[LifetimeStorage::class] as $store) {
                $store->clearOutdated();
            }
        }
    }

    private function runCommand($command, ConnectionInterface $connection)
    {
        list($alias, $path, $method, $args) = $command;
        $class = Factory::getClassByAlias($alias);

        if (null !== $this->logger) {
            $this->log(
                'Command from ' . $connection->getRemoteAddress() .
                ": pure.$alias.$path.$method(" .
                join(', ', array_map('json_encode', $args)) .
                ')'
            );
        }

        if (!isset($this->stores[$class][$path])) {
            $this->stores[$class][$path] = new $class;
        }

        $call = [$this->stores[$class][$path], $method];

        try {
            $result = call_user_func_array($call, $args);
        } catch (\Exception $e) {
            $result = null;
            $this->log('Exception: ' . $e->getMessage());
        }

        $connection->write(json_encode($result) . self::END_OF_RESULT);
    }

    public function log($message)
    {
        if (is_callable($this->logger)) {
            $this->logger->__invoke($message);
        }
    }

    public function setLogger(\Closure $callback)
    {
        $this->logger = $callback;
    }
}