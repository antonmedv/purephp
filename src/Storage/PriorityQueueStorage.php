<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Storage;

use Pure\Helper;

class PriorityQueueStorage extends \SplPriorityQueue implements StorageInterface
{
    use Helper\All;
    use Helper\Filter;
}
