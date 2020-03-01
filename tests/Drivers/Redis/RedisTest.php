<?php namespace AAD\Cache\Drivers\Redis;

use PHPUnit\Framework\TestCase;

final class RedisTest extends TestCase
{
    public function testInit()
    {
        $conn = $this->getMockBuilder(\Redis::class)->disableOriginalConstructor()->getMock();

        /**
         * @var \Redis $conn
         **/
        $redis = Redis::init($conn);

        $this->assertInstanceOf("AAD\Cache\Drivers\Redis\Redis", $redis);
    }

    public function testSet()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['set'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('set')
                ->will($this->returnValue(true));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->set('test_key', 'test_val');
        $this->assertTrue($response);
    }

    public function testHmset()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['hmset'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('hmset')
                ->will($this->returnValue(true));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->hmset('test_hash', ['test_key_1' => ['item_1', 'item_2'], 'test_key_2' => 22]);
        $this->assertTrue($response);
    }

    public function testFlushall()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['flushall'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('flushall')
                ->will($this->returnValue(true));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->flushall();
        $this->assertTrue($response);
    }
}
