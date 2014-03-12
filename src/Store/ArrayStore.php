<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Store;

use Pure\Client;

class ArrayStore implements StoreInterface
{
    const alias = 'of';

    private $data = [];

    public function all()
    {
        return $this->data;
    }

    public function clear()
    {
        unset($this->data);
        $this->data = [];
    }

    public function push($array)
    {
        $this->data = array_merge($this->data, (array)$array);
        return true;
    }

    public function get($key)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        } else {
            return null;
        }
    }

    public function has($key)
    {
        return isset($this->data[$key]);
    }

    public function delete($key)
    {
        unset($this->data[$key]);
    }
} 