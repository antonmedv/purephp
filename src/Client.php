<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

use Pure\Store\ArrayStore;
use Pure\Store\PriorityQueueStore;
use Pure\Store\QueueStore;
use Pure\Store\StackStore;

class Client
{
    const END_OF_COMMAND = 'END_OF_COMMAND';

    const READ_SIZE = 4096;

    private $host;

    private $port;

    private $socket;

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
     * @param $path
     * @return ArrayStore
     */
    public function of($path)
    {
        return new Proxy($this, ArrayStore::class, $path);
    }

    /**
     * @param $path
     * @return PriorityQueueStore
     */
    public function priority($path)
    {
        return new Proxy($this, PriorityQueueStore::class, $path);
    }

    /**
     * @param $path
     * @return QueueStore
     */
    public function queue($path)
    {
        return new Proxy($this, QueueStore::class, $path);
    }

    /**
     * @param $path
     * @return StackStore
     */
    public function stack($path)
    {
        return new Proxy($this, StackStore::class, $path);
    }

    public function __get($name)
    {
        switch ($name) {
            case 'of':
                return new Proxy\Generator($this, ArrayStore::class);
            case 'priority':
                return new Proxy\Generator($this, PriorityQueueStore::class);
            case 'queue':
                return new Proxy\Generator($this, QueueStore::class);
            case 'stack':
                return new Proxy\Generator($this, StackStore::class);
            default:
                throw new \RuntimeException("There are no `$name` store in PurePHP.");
        }
    }

    public function command($command)
    {
        $body = json_encode($command) . self::END_OF_COMMAND;
        socket_write($this->socket, $body, strlen($body));

        $data = null;

        $buffer = '';
        while ($read = socket_read($this->socket, self::READ_SIZE)) {
            $buffer .= $read;

            if (strpos($buffer, Server::END_OF_RESULT)) {
                $chunks = explode(Server::END_OF_RESULT, $buffer, 2);
                $data = json_decode($chunks[0], true);
                break;
            }
        }

        return $data;
    }

    public function close()
    {
        socket_close($this->socket);
    }
} 