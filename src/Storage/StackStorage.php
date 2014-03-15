<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Storage;

use Pure\Helper\Filter;
use Pure\Server;

class StackStorage extends \SplStack implements StorageInterface
{
    use Filter;

    const alias = 'stack';

    private $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function all()
    {
        $array = [];
        foreach ($this as $item) {
            $array[] = $item;
        }
        return $array;
    }

    public function clear()
    {
        while ($this->valid()) {
            $this->pop();
        }
    }
}