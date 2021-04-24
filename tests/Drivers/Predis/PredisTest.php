<?php

namespace AAD\Cache\Drivers\Predis;

use PHPUnit\Framework\TestCase;
use Predis\Client as PredisClient;

final class PredisTest extends TestCase
{
    public function testInit()
    {
        $conn = $this->getMockBuilder(PredisClient::class)->disableOriginalConstructor()->getMock();

        /**
         * @var PredisClient $conn
         **/
        $predis = Predis::init($conn);

        $this->assertInstanceOf("AAD\Cache\Drivers\Predis\Predis", $predis);
    }

    public function testSet()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['set'])
            ->getMock();

        $conn->expects($this->once())
            ->method('set')
            ->will($this->returnCallback(
                function () {
                    return new \Predis\Response\Status('OK');
                }
            ));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->set('test_key', 'test_val', null);
        $this->assertTrue($response);
    }

    public function testSetWithTtl()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['setex'])
            ->getMock();

        $conn->expects($this->once())
            ->method('setex')
            ->will($this->returnCallback(
                function () {
                    return new \Predis\Response\Status('OK');
                }
            ));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->set('test_key_2', 'test_val_2', 500);
        $this->assertTrue($response);
    }

    public function testSetWithZeroTtl()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->set('test_key_2', 'test_val_2', 0);
        $this->assertTrue($response);
    }

    public function testGet()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $conn->expects($this->once())
            ->method('get')
            ->will($this->returnValue(\serialize('test_val')));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->get('test_key');
        $this->assertEquals('test_val', $response);
    }

    public function testGetNumeric()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['get'])
            ->getMock();

        $conn->expects($this->once())
            ->method('get')
            ->will($this->returnValue(25));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->get('test_key');
        $this->assertEquals(25, $response);
    }

    public function testDel()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['del'])
            ->getMock();

        $conn->expects($this->once())
            ->method('del')
            ->will($this->returnValue(true));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->del('test_key');
        $this->assertTrue($response);
    }

    public function testHset()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['hset'])
            ->getMock();

        $conn->expects($this->once())
            ->method('hset')
            ->will($this->returnValue(true));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->hset('test_hash', 'test_key', 'test_val');
        $this->assertTrue($response);
    }

    public function testHget()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['hget'])
            ->getMock();

        $conn->expects($this->once())
            ->method('hget')
            ->will($this->returnValue(\serialize('test_val')));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->hget('test_hash', 'test_key');
        $this->assertEquals('test_val', $response);
    }

    public function testHgetNumeric()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['hget'])
            ->getMock();

        $conn->expects($this->once())
            ->method('hget')
            ->will($this->returnValue(25));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->hget('test_hash', 'test_key');
        $this->assertEquals(25, $response);
    }

    public function testHgetBoolean()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['hget'])
            ->getMock();

        $conn->expects($this->once())
            ->method('hget')
            ->will($this->returnValue(false));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->hget('test_hash', 'test_unknown_key');
        $this->assertFalse($response);
    }

    public function testHdel()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['hdel'])
            ->getMock();

        $conn->expects($this->once())
            ->method('hdel')
            ->will($this->returnValue(true));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->hdel('test_hash', 'test_key');
        $this->assertTrue($response);
    }

    public function testHmset()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['hmset'])
            ->getMock();

        $conn->expects($this->once())
            ->method('hmset')
            ->will($this->returnCallback(
                function () {
                    return new \Predis\Response\Status('OK');
                }
            ));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->hmset('test_hash', ['test_key_1' => ['item_1', 'item_2'], 'test_key_2' => 22]);
        $this->assertTrue($response);
    }

    public function testHgetall()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['hgetall'])
            ->getMock();

        $conn->expects($this->once())
            ->method('hgetall')
            ->will($this->returnValue([\serialize(['item_1', 'item_2']), 22]));

        /**
         * @var PredisClient $conn
         * @var array $response
         **/
        $response = Predis::init($conn)->hgetall('test_hash');
        $this->assertIsArray($response);
        $this->assertIsNumeric(array_search(22, $response));
        $this->assertEquals(2, count($response));
    }

    public function testHgetallNumeric()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['hgetall'])
            ->getMock();

        $conn->expects($this->once())
            ->method('hgetall')
            ->will($this->returnValue([22]));

        /**
         * @var PredisClient $conn
         * @var array $response
         **/
        $response = Predis::init($conn)->hgetall('test_hash');
        $this->assertIsArray($response);
        $this->assertEquals(1, count($response));
    }

    public function testExpire()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['expire'])
            ->getMock();

        $conn->expects($this->once())
            ->method('expire')
            ->will($this->returnValue(true));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->expire('test_hash', 180);
        $this->assertTrue($response);
    }

    public function testTtl()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['ttl'])
            ->getMock();

        $conn->expects($this->once())
            ->method('ttl')
            ->will($this->returnValue(180));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->ttl('test_hash');
        $this->assertEquals(180, $response);
    }

    public function testExists()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['exists'])
            ->getMock();

        $conn->expects($this->once())
            ->method('exists')
            ->will($this->returnValue(true));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->exists('test_hash');
        $this->assertTrue($response);
    }

    public function testFlushall()
    {
        $conn = $this->getMockBuilder(PredisClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['flushall'])
            ->getMock();

        $conn->expects($this->once())
            ->method('flushall')
            ->will($this->returnCallback(
                function () {
                    return new \Predis\Response\Status('OK');
                }
            ));

        /**
         * @var PredisClient $conn
         **/
        $response = Predis::init($conn)->flushall();
        $this->assertTrue($response);
    }
}
