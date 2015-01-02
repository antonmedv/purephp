<?php
/* (c) Anton Medvedev <anton@elfet.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure\Storage;

class StackTest extends \PHPUnit_Framework_TestCase
{
    public function testPushAndPop()
    {
        $queue = new StackStorage();
        $queue->push(1);
        $queue->push(2);
        $queue->push(3);

        $this->assertEquals(3, $queue->pop());
        $this->assertEquals(2, $queue->pop());
        $this->assertEquals(1, $queue->pop());
    }
}
 