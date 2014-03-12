<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

use Pure\Store\StoreFactory;

class Proxy
{
    private $client;

    private $alias;

    private $reflectionClass;

    private $path;

    public function __construct(Client $client, $alias, $path)
    {
        $this->client = $client;
        $this->alias = $alias;
        $this->reflectionClass = new \ReflectionClass(StoreFactory::getClassByAlias($alias));
        $this->path = $path;
    }

    public function __call($name, $arguments)
    {
        if ($this->reflectionClass->hasMethod($name)) {
            return $this->client->command([$this->alias, $this->path, $name, $arguments]);
        } else {
            $class = StoreFactory::getClassByAlias($this->alias);
            throw new \RuntimeException("Class `{$class}` does not have method `{$name}`.");
        }
    }
}