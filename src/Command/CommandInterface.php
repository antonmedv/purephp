<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Command;

use Pure\Server;
use React\Socket\ConnectionInterface;

interface CommandInterface
{
    /**
     * @param Server $server
     */
    public function __construct(Server $server);

    /**
     * @param $arguments
     * @param ConnectionInterface $connection
     * @return mixed
     */
    public function run($arguments, ConnectionInterface $connection);
}
