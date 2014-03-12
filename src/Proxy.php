<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

class Proxy
{
    private $client;

    private $class;

    private $reflectionClass;

    private $path;

    public function __construct(Client $client, $class, $path)
    {
        $this->client = $client;
        $this->class = $class;
        $this->reflectionClass = new \ReflectionClass($class);
        $this->path = $path;
    }

    public function __call($name, $arguments)
    {
        if ($this->reflectionClass->hasMethod($name)) {
            return $this->client->command([$this->class, $this->path, $name, $arguments]);
        } else {
            throw new \RuntimeException("Class `{$this->class}` does not have method `{$name}`.");
        }
    }
}