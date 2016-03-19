<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Command;

use Pure\Server;
use React\Socket\ConnectionInterface;

class PingCommand implements CommandInterface
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
     * Ping-Pong
     *
     * @param array $arguments
     * @param ConnectionInterface $connection
     * @return string
     */
    public function run($arguments, ConnectionInterface $connection)
    {
        list($ping) = $arguments;

        if ($ping === 'ping') {
            return 'pong';
        } else {
            throw new \RuntimeException("Ping command must receive `ping` instead of `$ping`.");
        }
    }
}
