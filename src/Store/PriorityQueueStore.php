<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Store;

class PriorityQueueStore extends \SplPriorityQueue implements StoreInterface
{
    const alias = 'priority';

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
            $this->extract();
        }
    }
}