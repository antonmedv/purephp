<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

use Pure\Console\ClientCommand;
use Pure\Console\StartCommand;
use Symfony\Component\Console\Application;

class Pure
{
    public static function run()
    {
        $console = new Application('PurePHP', '0.1.0');
        $console->add(new StartCommand());
        $console->add(new ClientCommand());
        $console->run();
    }
} 