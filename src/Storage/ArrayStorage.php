<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Storage;

use Pure\Helper\Filter;
use Pure\Server;

class ArrayStorage implements StorageInterface
{
    use Filter;

    const alias = 'of';

    private $data = [];

    private $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function all()
    {
        return $this->data;
    }

    public function clear()
    {
        unset($this->data);
        $this->data = [];
        return true;
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
        return true;
    }

    public function count()
    {
        return count($this->data);
    }
}