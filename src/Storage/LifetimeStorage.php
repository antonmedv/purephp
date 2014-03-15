<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Storage;

use Pure\Helper\Filter;
use Pure\Server;

class LifetimeStorage implements StorageInterface
{
    use Filter;

    const alias = 'lifetime';

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

    public function clearOutdated()
    {
        foreach($this->data as $key => $store) {
            list($value, $lifetime) = $store;

            if(time() >= $lifetime) {
                unset($this->data[$key]);
            }
        }
    }

    public function set($key, $value, $lifetime = 0)
    {
        $this->data[$key] = [$value, time() + $lifetime];
        return true;
    }

    public function get($key)
    {
        if($this->has($key)) {
            list($value, $lifetime) = $this->data[$key];
            return $value;
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