<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

class Proxy
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $class;

    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * @var string
     */
    private $name;

    /**
     * @param Client $client
     * @param string $class
     * @param string $name
     */
    public function __construct(Client $client, $class, $name)
    {
        $this->client = $client;
        $this->class = $class;
        $this->reflectionClass = new \ReflectionClass($class);
        $this->name = $name;
    }

    /**
     * @param string $method
     * @param array $arguments
     * @return mixed
     * @throws \RuntimeException
     */
    public function __call($method, $arguments)
    {
        if ($this->reflectionClass->hasMethod($method)) {
            return $this->client->command(['Pure\Command\StorageCommand', $this->class, $this->name, $method, $arguments]);
        } else {
            throw new \RuntimeException("Class `{$this->class}` does not have method `{$method}`.");
        }
    }
}
