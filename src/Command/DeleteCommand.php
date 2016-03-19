<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Command;

use Pure\Server;
use React\Socket\ConnectionInterface;

class DeleteCommand implements CommandInterface
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
     * Run delete storage command.
     *
     * @param array $arguments
     * @param ConnectionInterface $connection
     * @return bool
     */
    public function run($arguments, ConnectionInterface $connection)
    {
        list($name) = $arguments;

        if (isset($this->server[$name])) {
            unset($this->server[$name]);
            return true;
        } else {
            return false;
        }
    }
}
