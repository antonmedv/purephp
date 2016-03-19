<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Storage;

use Pure\Helper;

class QueueStorage extends \SplQueue implements StorageInterface
{
    use Helper\All;
    use Helper\Filter;

    /**
     * Dequeue queue.
     * 
     * @return mixed
     */
    public function pop()
    {
        return $this->dequeue();
    }

    /**
     * Enqueue queue.
     * 
     * @param mixed $value
     */
    public function push($value)
    {
        $this->enqueue($value);
    }
}
