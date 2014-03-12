<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Proxy;

use Pure\Proxy;

class Generator
{
    private $client;
    private $class;

    public function __construct($client, $class)
    {
        $this->client = $client;
        $this->class = $class;
    }

    public function __get($path)
    {
        return new Proxy($this->client, $this->class, $path);
    }
}