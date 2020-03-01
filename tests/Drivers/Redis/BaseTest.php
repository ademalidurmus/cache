<?php namespace AAD\Cache\Drivers\Redis;

use PHPUnit\Framework\TestCase;

final class BaseTest extends TestCase
{
    public function testGet()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['get'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('get')
                ->will($this->returnValue(\serialize('test_val')));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->get('test_key');
        $this->assertEquals('test_val', $response);
    }

    public function testGetNumeric()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['get'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('get')
                ->will($this->returnValue(25));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->get('test_key');
        $this->assertEquals(25, $response);
    }

    public function testDel()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['del'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('del')
                ->will($this->returnValue(true));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->del('test_key');
        $this->assertTrue($response);
    }

    public function testHset()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['hset'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('hset')
                ->will($this->returnValue(true));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->hset('test_hash', 'test_key', 'test_val');
        $this->assertTrue($response);
    }

    public function testHget()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['hget'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('hget')
                ->will($this->returnValue(\serialize('test_val')));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->hget('test_hash', 'test_key');
        $this->assertEquals('test_val', $response);
    }

    public function testHgetNumeric()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['hget'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('hget')
                ->will($this->returnValue(25));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->hget('test_hash', 'test_key');
        $this->assertEquals(25, $response);
    }

    public function testHgetBoolean()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['hget'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('hget')
                ->will($this->returnValue(false));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->hget('test_hash', 'test_unknown_key');
        $this->assertFalse($response);
    }

    public function testHdel()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['hdel'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('hdel')
                ->will($this->returnValue(true));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->hdel('test_hash', 'test_key');
        $this->assertTrue($response);
    }

    public function testHgetall()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['hgetall'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('hgetall')
                ->will($this->returnValue([\serialize(['item_1', 'item_2']), 22]));

        /**
         * @var \Redis $conn
         * @var array $response
         **/
        $response = Redis::init($conn)->hgetall('test_hash');
        $this->assertIsArray($response);
        $this->assertIsNumeric(array_search(22, $response));
        $this->assertEquals(2, count($response));
    }

    public function testHgetallNumeric()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['hgetall'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('hgetall')
                ->will($this->returnValue([22]));

        /**
         * @var \Redis $conn
         * @var array $response
         **/
        $response = Redis::init($conn)->hgetall('test_hash');
        $this->assertIsArray($response);
        $this->assertEquals(1, count($response));
    }

    public function testExpire()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['expire'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('expire')
                ->will($this->returnValue(true));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->expire('test_hash', 180);
        $this->assertTrue($response);
    }

    public function testTtl()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['ttl'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('ttl')
                ->will($this->returnValue(180));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->ttl('test_hash');
        $this->assertEquals(180, $response);
    }

    public function testExists()
    {
        $conn = $this->getMockBuilder(\Redis::class)
                        ->disableOriginalConstructor()
                        ->setMethods(['exists'])
                        ->getMock();

        $conn->expects($this->once())
                ->method('exists')
                ->will($this->returnValue(true));

        /**
         * @var \Redis $conn
         **/
        $response = Redis::init($conn)->exists('test_hash');
        $this->assertTrue($response);
    }
}
