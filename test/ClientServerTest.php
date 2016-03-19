<?php
/* (c) Anton Medvedev <anton@medv.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pure;

use Symfony\Component\Process\Process;

class ClientServerTest extends \PHPUnit_Framework_TestCase
{
    const PORT = 1337;

    static $process;

    public static function setUpBeforeClass()
    {
        self::$process = new Process('php ' . __DIR__ . '/../pure start --port=' . self::PORT);
        self::$process->start();

        sleep(1);
    }

    public static function tearDownAfterClass()
    {
        self::$process->stop(3, SIGKILL);
    }

    public function testPing()
    {
        $client = new Client(self::PORT);

        $this->assertTrue($client->ping());
    }

    public function testQueue()
    {
        $client = new Client(self::PORT);

        $client->queue('test')->push(1);
        $client->queue('test')->push(2);
        $client->queue('test')->push(3);

        $this->assertEquals(1, $client->queue('test')->pop());
        $this->assertEquals(2, $client->queue('test')->pop());
        $this->assertEquals(3, $client->queue('test')->pop());
    }
}
