<?php namespace AAD\Cache\Drivers\Files;

use AAD\Cache\CacheTest;
use PHPUnit\Framework\TestCase;

final class FilesTest extends TestCase
{
    const TEST_HASH = 'd1b477f4feaa0a3c379aa2e3692973d1060440b8';
    const TEST_CACHE_DIR = __DIR__ . "/../../../_cache_files_test";
    const TEST_CONFIG = [
        'cache_dir' => __DIR__ . "/../../../_cache_files_test",
        'cache_ttl' => 180,
    ];

    public function testInit()
    {
        Files::init(self::TEST_CONFIG);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
    }

    public function testSetConfig()
    {
        $files = new Files();
        $files->setConfig(self::TEST_CONFIG);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
    }

    public function testSetDir()
    {
        $files = new Files();
        $files->setConfig()->setCacheDir(self::TEST_CACHE_DIR);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
    }

    public function testGetCacheDir()
    {
        $files = new Files();
        $cache_dir = $files->setConfig(self::TEST_CONFIG)->getCacheDir();
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
        $this->assertEquals($cache_dir, self::TEST_CACHE_DIR);
    }

    public function testGetCacheDirForHash()
    {
        $files = new Files();
        $cache_dir = $files->setConfig(self::TEST_CONFIG)->getCacheDir(self::TEST_HASH);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
        $this->assertEquals($cache_dir, self::TEST_CACHE_DIR . "/d1/b4");
    }

    public function testGetCacheFilePath()
    {
        $files = new Files();
        $cache_dir = $files->setConfig(self::TEST_CONFIG)->getCacheDir(self::TEST_HASH);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
        $this->assertEquals($cache_dir, self::TEST_CACHE_DIR . "/d1/b4");
    }

    public function testSet()
    {
        $files = new Files();
        $response = $files->setConfig(self::TEST_CONFIG)->set('test_key', 'test_val');
        $this->assertTrue($response);
    }

    public function testGet()
    {
        $files = new Files();
        $response = $files->setConfig(self::TEST_CONFIG)->get('test_key');
        $this->assertEquals($response, 'test_val');
    }

    public function testSetWithTTL()
    {
        $files = new Files();
        $files->setConfig(self::TEST_CONFIG)->set('test_key', 'test_val', 1);

        sleep(2);

        $files = new Files();
        $response = $files->setConfig(self::TEST_CONFIG)->get('test_key');
        $this->assertFalse($response);
    }

    public function testGetUnknownData()
    {
        $files = new Files();
        $response = $files->setConfig(self::TEST_CONFIG)->get('test_key');
        $this->assertFalse($response);
    }

    public function testGetJsonError()
    {
        $files = new Files();
        $file_path = $files->setConfig(self::TEST_CONFIG)->getCacheFilePath('test_key');

        @file_put_contents($file_path, '{key":"test_key","value":"s:8:\"test_val\";","expire_time":1582571518,"create_time":1582571518,"update_time":1582571518,"hash":"96aadd7725f0a503bd890ae1a2e4fb3e8fa328f1","method":"set"}');

        $response = $files->setConfig(self::TEST_CONFIG)->get('test_key');
        $this->assertFalse($response);
    }

    public function testGetWrongHash()
    {
        $files = new Files();
        $file_path = $files->setConfig(self::TEST_CONFIG)->getCacheFilePath('test_key_1');

        @file_put_contents($file_path, '{"key":"test_key_1","value":"s:8:\"test_val\";","expire_time":' . time() + 3600 . ',"create_time":1582571518,"update_time":1582571518,"hash":"96aadd7725f0a503bd890ae1a2e4fb3e8fa328f1","method":"set"}');

        $response = $files->setConfig(self::TEST_CONFIG)->get('test_key_1');
        $this->assertFalse($response);
    }

    public function testDel()
    {
        $response = Files::init(self::TEST_CONFIG)->set('test_key_2', 'test_val_2');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->del('test_key_2');
        $this->assertTrue($response);
    }

    public function testDelUnknownKey()
    {
        $response = Files::init(self::TEST_CONFIG)->del('test_key_2');
        $this->assertFalse($response);
    }

    public function testHsetHget()
    {
        $response = Files::init(self::TEST_CONFIG)->hset('test_hash', 'test_key_1', 'test_val_1');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->hget('test_hash', 'test_key_1');
        $this->assertEquals('test_val_1', $response);
    }

    public function testHsetUpdateValue()
    {
        $response = Files::init(self::TEST_CONFIG)->hset('test_hash', 'test_key_1', 'test_val_1_update');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->hget('test_hash', 'test_key_1');
        $this->assertEquals('test_val_1_update', $response);
    }

    public function testHgetJsonError()
    {
        $file_path = Files::init(self::TEST_CONFIG)->getCacheFilePath('test_hash');

        @file_put_contents($file_path, '{hash":"327d106bf608b1f63bf5cbc5d1b6ea2d6836b446","value":{"test_key_1":"s:17:\"test_val_1\";"},"expire_time":-1,"create_time":1582653164,"update_time":1582653164,"method":"hset"}');

        $response = Files::init(self::TEST_CONFIG)->hget('test_hash', 'test_key_1');
        $this->assertFalse($response);
    }

    public function testHgetWrongHash()
    {
        $file_path = Files::init(self::TEST_CONFIG)->getCacheFilePath('test_wrong_hash');

        @file_put_contents($file_path, '{"hash":"327d106bf608b1f63bf5cbc5d1b6ea2d6836b446","value":{"test_key_1":"s:17:\"test_val_1\";"},"expire_time":-1,"create_time":1582653164,"update_time":1582653164,"method":"hset"}');

        $response = Files::init(self::TEST_CONFIG)->hget('test_wrong_hash', 'test_key_1');
        $this->assertFalse($response);
    }

    public function testHgetUnknownKey()
    {
        Files::init(self::TEST_CONFIG)->del('test_hash');

        $response = Files::init(self::TEST_CONFIG)->hget('test_hash', 'test_key_1');
        $this->assertFalse($response);
    }
    
    public function testHdel()
    {
        $response = Files::init(self::TEST_CONFIG)->hset('test_new_hash', 'test_key_1', 'test_val_1');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->hset('test_new_hash', 'test_key_2', 'test_val_2');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->hget('test_new_hash', 'test_key_2');
        $this->assertEquals('test_val_2', $response);

        $response = Files::init(self::TEST_CONFIG)->hdel('test_new_hash', 'test_key_2');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->hget('test_new_hash', 'test_key_2');
        $this->assertFalse($response);

        $response = Files::init(self::TEST_CONFIG)->hdel('test_new_hash', 'test_key_1');
        $this->assertFalse(file_exists(self::TEST_CACHE_DIR . "/te/st/test_new_hash"));
    }

    public function testHdelUnknownHash()
    {
        $response = Files::init(self::TEST_CONFIG)->hdel('test_unknown_hash', 'test_key_2');
        $this->assertFalse($response);
    }

    public function testHdelJsonError()
    {
        $file_path = Files::init(self::TEST_CONFIG)->getCacheFilePath('test_hash');

        @file_put_contents($file_path, '{hash":"327d106bf608b1f63bf5cbc5d1b6ea2d6836b446","value":{"test_key_1":"s:17:\"test_val_1\";"},"expire_time":-1,"create_time":1582653164,"update_time":1582653164,"method":"hset"}');

        $response = Files::init(self::TEST_CONFIG)->hdel('test_hash', 'test_key_1');
        $this->assertFalse($response);
    }

    public function testHdelWrongHash()
    {
        $file_path = Files::init(self::TEST_CONFIG)->getCacheFilePath('test_wrong_hash');

        @file_put_contents($file_path, '{"hash":"327d106bf608b1f63bf5cbc5d1b6ea2d6836b446","value":{"test_key_1":"s:17:\"test_val_1\";","test_key_2":"s:17:\"test_val_2\";"},"expire_time":-1,"create_time":1582653164,"update_time":1582653164,"method":"hset"}');

        $response = Files::init(self::TEST_CONFIG)->hdel('test_wrong_hash', 'test_key_1');
        $this->assertFalse($response);
        $this->assertFalse(file_exists(self::TEST_CACHE_DIR . "/te/st/test_wrong_hash"));
    }

    public function testHmset()
    {
        $response = Files::init(self::TEST_CONFIG)->hmset('test_hm_hash', ['test_key_1' => 'test_val_1', 'test_key_2' => 'test_val_2']);
        $this->assertTrue($response);
    }

    public function testHgetall()
    {
        $response = Files::init(self::TEST_CONFIG)->hgetall('test_hm_hash');
        $this->assertIsArray($response);
        $this->assertEquals(2, count($response));
        $this->assertTrue($response[0] === 'test_val_1' || $response[1] === 'test_val_1');
    }

    public function testExpire()
    {
        $response = Files::init(self::TEST_CONFIG)->expire('test_hm_hash', 1500);
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->expire('test_unknown_key', 1500);
        $this->assertFalse($response);
    }

    public function testTtl()
    {
        $response = Files::init(self::TEST_CONFIG)->ttl('test_hm_hash');
        $this->assertTrue($response > 0 && $response < 1501);

        $response = Files::init(self::TEST_CONFIG)->ttl('test_unknown_hash');
        $this->assertEquals(-2, $response);

        $response = Files::init(self::TEST_CONFIG)->set('test_new_key_1', 'test_val', -1);
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->ttl('test_new_key_1');
        $this->assertEquals(-1, $response);

        $response = Files::init(self::TEST_CONFIG)->set('test_new_key_2', 'test_val', 0);
        $this->assertTrue($response);
        $this->assertTrue(file_exists(self::TEST_CACHE_DIR . CacheTest::_getHash('test_new_key_2')));
        sleep(1);
        $response = Files::init(self::TEST_CONFIG)->ttl('test_new_key_2');
        $this->assertEquals(-2, $response);
        $this->assertFalse(file_exists(self::TEST_CACHE_DIR . CacheTest::_getHash('test_new_key_2')));
    }

    public function testExists()
    {
        $response = Files::init(self::TEST_CONFIG)->set('test_exists_key', 'test_val');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->exists('test_exists_key');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->del('test_exists_key');
        $this->assertTrue($response);
 
        $response = Files::init(self::TEST_CONFIG)->exists('test_exists_key');
        $this->assertFalse($response);

        $response = Files::init(self::TEST_CONFIG)->set('test_exists_key_with_ttl', 'test_val', 0);
        $this->assertTrue($response);
        sleep(1);
        $response = Files::init(self::TEST_CONFIG)->exists('test_exists_key_with_ttl');
        $this->assertFalse($response);
    }

    public function testFlusall()
    {
        $response = Files::init(self::TEST_CONFIG)->set('test_flushall_key', 'test_val');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->exists('test_flushall_key');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->flushall();
        $this->assertTrue($response);
 
        $response = Files::init(self::TEST_CONFIG)->exists('test_flushall_key');
        $this->assertFalse($response);
    }

    public function testHsetHdelForExistingSetMethodKey()
    {
        $response = Files::init(self::TEST_CONFIG)->set('test_hash', 'test_val');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->hset('test_hash', 'test_key', 'test_val');
        $this->assertFalse($response);

        $response = Files::init(self::TEST_CONFIG)->hdel('test_hash', 'test_key');
        $this->assertFalse($response);

        $response = Files::init(self::TEST_CONFIG)->exists('test_hash');
        $this->assertTrue($response);
    }


    public function testSetForExistingHsetMethodKey()
    {
        $response = Files::init(self::TEST_CONFIG)->flushall();
        $this->assertTrue($response);
 
        $response = Files::init(self::TEST_CONFIG)->hset('test_hash', 'test_key', 'test_val');
        $this->assertTrue($response);

        $response = Files::init(self::TEST_CONFIG)->set('test_hash', 'test_val');
        $this->assertFalse($response);
    }

    public function testRemoveDir()
    {
        if (is_dir(self::TEST_CACHE_DIR)) {
            \AAD\Cache\Drivers\Files\Helper::remove(self::TEST_CACHE_DIR);
        }
        $this->assertFalse(is_dir(self::TEST_CACHE_DIR));
    }
}
