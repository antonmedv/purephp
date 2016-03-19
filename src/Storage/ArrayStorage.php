<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Storage;

use Pure\Helper;

class ArrayStorage extends \ArrayIterator implements StorageInterface
{
    use Helper\All;
    use Helper\Filter;

    /**
     * @param array $array
     */
    public function push($array)
    {
        foreach ((array)$array as $key => $value) {
            $this[$key] = $value;
        }
    }

    /**
     * @param mixed $key
     * @return null
     */
    public function get($key)
    {
        if ($this->has($key)) {
            return $this[$key];
        } else {
            return null;
        }
    }

    /**
     * @param mixed $key
     * @return bool
     */
    public function has($key)
    {
        return isset($this[$key]);
    }

    /**
     * Delete element by key.
     *
     * @param mixed $key
     */
    public function delete($key)
    {
        unset($this[$key]);
    }
}
