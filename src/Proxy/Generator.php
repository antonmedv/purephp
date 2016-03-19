<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Proxy;

use Pure\Client;
use Pure\Proxy;

/**
 * Class help to generate proxy object when accessing pure storage like what `$pure->stack->name->pop()`. 
 */
class Generator
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $alias;

    /**
     * @param Client $client
     * @param $alias
     */
    public function __construct(Client $client, $alias)
    {
        $this->client = $client;
        $this->alias = $alias;
    }

    /**
     * @param $name
     * @return \Pure\Storage\StorageInterface
     */
    public function __get($name)
    {
        return $this->client->{$this->alias}($name);
    }
}
