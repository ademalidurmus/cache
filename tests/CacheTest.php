<?php namespace AAD\Cache;

use PHPUnit\Framework\TestCase;

final class CacheTest extends TestCase
{
    const TEST_CACHE_DIR = __DIR__ . "/_cache_files";
    const TEST_CONFIG = [
        'cache_dir' => __DIR__ . "/_cache_files",
        'cache_ttl' => 180,
    ];

    public function testInit()
    {
        Cache::init(File::init(self::TEST_CONFIG));
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
    }

    public function testSet()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->set('test_key', 'test_val');
        $this->assertTrue($response);
    }

    public function testSetDefaultTtl()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->set('test_key', 'test_val', -2);
        $this->assertTrue($response);
    }

    public function testExpire()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->expire('test_key', 100);
        $this->assertTrue($response);
    }

    public function testTtl()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->ttl('test_key');
        $this->assertTrue($response > 0 && $response < 101);
    }

    public function testExpireDefault()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->expire('test_key', -2);
        $this->assertTrue($response);

        $response = Cache::init(File::init(self::TEST_CONFIG))->ttl('test_key');
        $this->assertTrue($response === -1);
    }

    public function testGet()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->get('test_key');
        $this->assertEquals('test_val', $response);
    }

    public function testKeyCleaner()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->set('test key 1', 'test_val');
        $this->assertTrue($response);

        $response = Cache::init(File::init(self::TEST_CONFIG))->get('test key 1');
        $this->assertEquals('test_val', $response);

        $response = Cache::init(File::init(self::TEST_CONFIG))->get('test_key_1');
        $this->assertEquals('test_val', $response);
    }

    public function testDel()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->set('test_key_2', 'test_val_2');
        $this->assertTrue($response);
        $this->assertTrue(file_exists(self::TEST_CACHE_DIR . "/te/st/test_key_2"));

        $response = Cache::init(File::init(self::TEST_CONFIG))->del('test_key_2');
        $this->assertTrue(!file_exists(self::TEST_CACHE_DIR . "/te/st/test_key_2"));
    }

    public function testDelUnknownKey()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->del('test_key_2');
        $this->assertFalse($response);
    }

    public function testHset()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->hset('test_hash', 'test_key_1', 'test_val_1');
        $this->assertTrue($response);
        $this->assertTrue(file_exists(self::TEST_CACHE_DIR . "/te/st/test_hash"));
    }

    public function testHget()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->hget('test_hash', 'test_key_1');
        $this->assertEquals('test_val_1', $response);
    }

    public function testHdel()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->hdel('test_hash', 'test_key_1');
        $this->assertTrue($response);
        $this->assertFalse(file_exists(self::TEST_CACHE_DIR . "/te/st/test_hash"));
    }

    public function testHmset()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->hmset('test_new_hash', ['test_key_1' => 'test_val_1', 'test_key_2' => 'test_val_2']);
        $this->assertTrue($response);
    }

    public function testHgetall()
    {
        $response = Cache::init(File::init(self::TEST_CONFIG))->hgetall('test_new_hash');
        $this->assertIsArray($response);
    }

    public function testRemoveDir()
    {
        if (is_dir(self::TEST_CACHE_DIR)) {
            \AAD\Cache\File\Helper::remove(self::TEST_CACHE_DIR);
        }
        $this->assertFalse(is_dir(self::TEST_CACHE_DIR));
    }
}
