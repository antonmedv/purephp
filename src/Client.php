<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

class Client
{
    const END_OF_COMMAND = 'END_OF_COMMAND';

    const READ_SIZE = 4096;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $host;

    /**
     * @var resource
     */
    private $socket;

    /**
     * @param int $port
     * @param string $host
     * @throws \RuntimeException
     */
    public function __construct($port, $host = '127.0.0.1')
    {
        $this->host = $host;
        $this->port = $port;

        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        if ($this->socket === false) {
            throw new \RuntimeException(socket_strerror(socket_last_error()));
        }

        $result = socket_connect($this->socket, $this->host, $this->port);
        if ($result === false) {
            throw new \RuntimeException(socket_strerror(socket_last_error($this->socket)));
        }
    }

    /**
     * @param string $name
     * @return \Pure\Storage\ArrayStorage
     */
    public function map($name)
    {
        return new Proxy($this, 'Pure\Storage\ArrayStorage', $name);
    }

    /**
     * @param string $name
     * @return \Pure\Storage\PriorityQueueStorage
     */
    public function priority($name)
    {
        return new Proxy($this, 'Pure\Storage\PriorityQueueStorage', $name);
    }

    /**
     * @param string $name
     * @return \Pure\Storage\QueueStorage
     */
    public function queue($name)
    {
        return new Proxy($this, 'Pure\Storage\QueueStorage', $name);
    }

    /**
     * @param string $name
     * @return \Pure\Storage\StackStorage
     */
    public function stack($name)
    {
        return new Proxy($this, 'Pure\Storage\StackStorage', $name);
    }

    /**
     * @param string $alias
     * @return Proxy\Generator
     * @throws \RuntimeException
     */
    public function __get($alias)
    {
        if (in_array($alias, ['map', 'priority', 'queue', 'stack'], true)) {
            return new Proxy\Generator($this, $alias);
        } else {
            throw new \RuntimeException("There are no method `$alias` in client class.");
        }
    }

    /**
     * @param array $command
     * @return mixed
     * @throws \RuntimeException
     */
    public function command($command)
    {
        $body = json_encode($command) . self::END_OF_COMMAND;
        @socket_write($this->socket, $body, strlen($body));

        $command = null;

        $buffer = '';
        while ($read = @socket_read($this->socket, self::READ_SIZE)) {
            $buffer .= $read;

            if (strpos($buffer, Server::END_OF_RESULT)) {
                $chunks = explode(Server::END_OF_RESULT, $buffer, 2);
                $command = json_decode($chunks[0], true);
                break;
            }
        }

        if (Server::RESULT === $command[0]) {
            return $command[1];
        } elseif (Server::EXCEPTION === $command[0]) {
            $class = $command[1];
            throw new $class($command[2]);
        } else {
            throw new \RuntimeException('Unknown command from server.');
        }
    }

    /**
     * Close socket.
     */
    public function close()
    {
        socket_close($this->socket);
    }

    /**
     * Delete storage on server.
     *
     * @param string $name
     * @return bool
     */
    public function delete($name)
    {
        return $this->command(['Pure\Command\DeleteCommand', $name]);
    }

    /**
     * Checks if server is alive.
     *
     * @return bool
     */
    public function ping()
    {
        try {
            $pong = $this->command(['Pure\Command\PingCommand', 'ping']);
        } catch (\RuntimeException $e) {
            return false;
        }

        return $pong === 'pong';
    }
}
