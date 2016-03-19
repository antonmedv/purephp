<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Command;

use Pure\Server;
use React\Socket\ConnectionInterface;

class StorageCommand implements CommandInterface
{
    /**
     * @var Server
     */
    private $server;

    /**
     * @param Server $server
     */
    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    /**
     * Runs storage command.
     * Command represented as array of next structure [class, name, method, args] where
     *  class - storage full class name,
     *  name - name of storage to store (every storage has to have unique name),
     *  method - method of storage to call,
     *  args - arguments for that method.
     *
     * @param array $arguments
     * @param ConnectionInterface $connection
     * @return array
     * @throws \RuntimeException
     */
    public function run($arguments, ConnectionInterface $connection)
    {
        list($class, $name, $method, $args) = $arguments;

        if (isset($this->server[$name])) {
            if (!$this->server[$name] instanceof $class) {
                throw new \RuntimeException("Storage `$name` has type `" . get_class($this->server[$name]) . "` (you request `$class`)");
            }
        } else {
            $this->server[$name] = new $class();
        }

        $call = [$this->server[$name], $method];
        $result = call_user_func_array($call, $args);

        return $result;
    }
}
