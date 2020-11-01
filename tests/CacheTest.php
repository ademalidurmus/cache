<?php namespace AAD\Cache;

use PHPUnit\Framework\TestCase;
use AAD\Cache\Drivers\Files\Files;
use Rhumsaa\Uuid\Uuid;

final class CacheTest extends TestCase
{
    const TEST_CACHE_DIR = __DIR__ . "/../_cache_files_test";
    const TEST_CONFIG = [
        'cache_dir' => __DIR__ . "/../_cache_files_test",
        'cache_ttl' => 180,
    ];

    public function testInit()
    {
        Cache::init(Files::init(self::TEST_CONFIG));
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
    }

    public function testSet()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->set('test_key', 'test_val');
        $this->assertTrue($response);
    }

    public function testSetDefaultTtl()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->set('test_key', 'test_val', -2);
        $this->assertTrue($response);
    }

    public function testExpire()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->expire('test_key', 100);
        $this->assertTrue($response);
    }

    public function testTtl()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->ttl('test_key');
        $this->assertTrue($response > 0 && $response < 101);
    }

    public function testExpireDefault()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->expire('test_key', -2);
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->ttl('test_key');
        $this->assertTrue($response === -1);
    }

    public function testGet()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->get('test_key');
        $this->assertEquals('test_val', $response);
    }

    public function testGetWithDefault()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->get('test_unknown_key');
        $this->assertFalse($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->get('test_unknown_key', 'default_val');
        $this->assertEquals('default_val', $response);
    }

    public function testExists()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('test_key');
        $this->assertTrue($response);
    }

    public function testKeyCleaner()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->set('test key 1', 'test_val');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->get('test key 1');
        $this->assertEquals('test_val', $response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->get('test_key_1');
        $this->assertEquals('test_val', $response);
    }

    public function testDel()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->set('test_key_2', 'test_val_2');
        $this->assertTrue($response);
        $this->assertTrue(file_exists(self::TEST_CACHE_DIR . self::_getHash('test_key_2')));

        $response = Cache::init(Files::init(self::TEST_CONFIG))->del('test_key_2');
        $this->assertTrue(!file_exists(self::TEST_CACHE_DIR . self::_getHash('test_key_2')));
    }

    public function testDelUnknownKey()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->del('test_key_2');
        $this->assertFalse($response);
    }

    public function testHset()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->hset('test_hash', 'test_key_1', 'test_val_1');
        $this->assertTrue($response);
        $this->assertTrue(file_exists(self::TEST_CACHE_DIR . self::_getHash('test_hash')));
    }

    public function testHget()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->hget('test_hash', 'test_key_1');
        $this->assertEquals('test_val_1', $response);
    }

    public function testHdel()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->hdel('test_hash', 'test_key_1');
        $this->assertTrue($response);
        $this->assertFalse(file_exists(self::TEST_CACHE_DIR . self::_getHash('test_hash')));
    }

    public function testHmset()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->hmset('test_new_hash', ['test_key_1' => 'test_val_1', 'test_key_2' => 'test_val_2']);
        $this->assertTrue($response);
    }

    public function testHgetall()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->hgetall('test_new_hash');
        $this->assertIsArray($response);
    }

    public function testClearAndFlushall()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('test_new_hash');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->clear();
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('test_new_hash');
        $this->assertFalse($response);
    }

    public function testDelete()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->set('test_key', 'test_val');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('test_key');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->delete('test_key');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('test_key');
        $this->assertFalse($response);
    }

    public function testSetMultiple()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->setMultiple(['multiple_key_1' => 'multiple_val_1', 'multiple_key_2' => 'multiple_val_2']);
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('multiple_key_1');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('multiple_key_2');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->setMultiple(['multiple_key_4' => 'multiple_val_4'], -200);
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->ttl('multiple_key_2');
        $this->assertEquals(self::TEST_CONFIG['cache_ttl'], $response);
    }

    public function testSetMultipleForExistingKey()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->hmset('test_new_hash', ['test_key_1' => 'test_val_1', 'test_key_2' => 'test_val_2']);
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->setMultiple(['test_new_hash' => 'test_val']);
        $this->assertFalse($response);
    }

    public function testHas()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->has('multiple_key_1');
        $this->assertTrue($response);
    }

    public function testGetMultiple()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('multiple_key_1');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('multiple_key_2');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('multiple_key_3');
        $this->assertFalse($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->getMultiple(['multiple_key_1', 'multiple_key_2', 'multiple_key_3'], 'default_val');
        $this->assertIsArray($response);
        $this->assertEquals('default_val', $response['multiple_key_3']);
    }

    public function testDeleteMultiple()
    {
        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('multiple_key_1');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('multiple_key_2');
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->deleteMultiple(['multiple_key_1', 'multiple_key_2']);
        $this->assertTrue($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('multiple_key_1');
        $this->assertFalse($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->exists('multiple_key_2');
        $this->assertFalse($response);

        $response = Cache::init(Files::init(self::TEST_CONFIG))->deleteMultiple(['multiple_key_1', 'multiple_key_2']);
        $this->assertFalse($response);
    }

    public function testRemoveDir()
    {
        if (is_dir(self::TEST_CACHE_DIR)) {
            \AAD\Cache\Drivers\Files\Helper::remove(self::TEST_CACHE_DIR);
        }
        $this->assertFalse(is_dir(self::TEST_CACHE_DIR));
    }

    public static function _getHash($key)
    {
        $hash = (string) Uuid::uuid5(Uuid::NIL, $key);
        return '/' . mb_substr($hash, 0, 2) . '/' . mb_substr($hash, 2, 2) . "/{$hash}";
    }
}
