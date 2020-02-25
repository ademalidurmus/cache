<?php namespace AAD\Cache;

use PHPUnit\Framework\TestCase;

final class FileTest extends TestCase
{
    const TEST_HASH = 'd1b477f4feaa0a3c379aa2e3692973d1060440b8';
    const TEST_CACHE_DIR = __DIR__ . "/_cache_files";
    const TEST_CONFIG = [
        'cache_dir' => __DIR__ . "/_cache_files",
        'cache_ttl' => 180,
    ];

    public function testInit()
    {
        File::init(self::TEST_CONFIG);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
    }

    public function testSetConfig()
    {
        $file = new File();
        $file->setConfig(self::TEST_CONFIG);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
    }

    public function testSetDir()
    {
        $file = new File();
        $file->setConfig()->setCacheDir(self::TEST_CACHE_DIR);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
    }

    public function testGetCacheDir()
    {
        $file = new File();
        $cache_dir = $file->setConfig(self::TEST_CONFIG)->getCacheDir();
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
        $this->assertEquals($cache_dir, self::TEST_CACHE_DIR);
    }

    public function testGetCacheDirForHash()
    {
        $file = new File();
        $cache_dir = $file->setConfig(self::TEST_CONFIG)->getCacheDir(self::TEST_HASH);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
        $this->assertEquals($cache_dir, self::TEST_CACHE_DIR . "/d1/b4");
    }

    public function testGetCacheFilePath()
    {
        $file = new File();
        $cache_dir = $file->setConfig(self::TEST_CONFIG)->getCacheDir(self::TEST_HASH);
        $this->assertTrue(is_dir(self::TEST_CACHE_DIR));
        $this->assertEquals($cache_dir, self::TEST_CACHE_DIR . "/d1/b4");
    }

    public function testSet()
    {
        $file = new File();
        $response = $file->setConfig(self::TEST_CONFIG)->set('test_key', 'test_val');
        $this->assertTrue($response);
    }

    public function testGet()
    {
        $file = new File();
        $response = $file->setConfig(self::TEST_CONFIG)->get('test_key');
        $this->assertEquals($response, 'test_val');
    }

    public function testSetWithTTL()
    {
        $file = new File();
        $file->setConfig(self::TEST_CONFIG)->set('test_key', 'test_val', 1);

        sleep(2);

        $file = new File();
        $response = $file->setConfig(self::TEST_CONFIG)->get('test_key');
        $this->assertFalse($response);
    }

    public function testGetUnknownData()
    {
        $file = new File();
        $response = $file->setConfig(self::TEST_CONFIG)->get('test_key');
        $this->assertFalse($response);
    }

    public function testGetJsonError()
    {
        $file = new File();
        $file_path = $file->setConfig(self::TEST_CONFIG)->getCacheFilePath('test_key');

        @file_put_contents($file_path, '{key":"test_key","value":"s:8:\"test_val\";","expire_time":1582571518,"create_time":1582571518,"update_time":1582571518,"hash":"96aadd7725f0a503bd890ae1a2e4fb3e8fa328f1"}');

        $response = $file->setConfig(self::TEST_CONFIG)->get('test_key');
        $this->assertFalse($response);
    }

    public function testGetWrongHash()
    {
        $file = new File();
        $file_path = $file->setConfig(self::TEST_CONFIG)->getCacheFilePath('test_key_1');

        @file_put_contents($file_path, '{"key":"test_key_1","value":"s:8:\"test_val\";","expire_time":' . time() + 3600 . ',"create_time":1582571518,"update_time":1582571518,"hash":"96aadd7725f0a503bd890ae1a2e4fb3e8fa328f1"}');

        $response = $file->setConfig(self::TEST_CONFIG)->get('test_key_1');
        $this->assertFalse($response);
    }

    public function testDel()
    {
        $response = File::init(self::TEST_CONFIG)->set('test_key_2', 'test_val_2');
        $this->assertTrue($response);

        $response = File::init(self::TEST_CONFIG)->del('test_key_2');
        $this->assertTrue($response);
    }

    public function testDelUnknownKey()
    {
        $response = File::init(self::TEST_CONFIG)->del('test_key_2');
        $this->assertFalse($response);
    }

    public function testHsetHget()
    {
        $response = File::init(self::TEST_CONFIG)->hset('test_hash', 'test_key_1', 'test_val_1');
        $this->assertTrue($response);

        $response = File::init(self::TEST_CONFIG)->hget('test_hash', 'test_key_1');
        $this->assertEquals('test_val_1', $response);
    }

    public function testHsetUpdateValue()
    {
        $response = File::init(self::TEST_CONFIG)->hset('test_hash', 'test_key_1', 'test_val_1_update');
        $this->assertTrue($response);

        $response = File::init(self::TEST_CONFIG)->hget('test_hash', 'test_key_1');
        $this->assertEquals('test_val_1_update', $response);
    }

    public function testHgetJsonError()
    {
        $file_path = File::init(self::TEST_CONFIG)->getCacheFilePath('test_hash');

        @file_put_contents($file_path, '{hash":"327d106bf608b1f63bf5cbc5d1b6ea2d6836b446","value":{"test_key_1":"s:17:\"test_val_1\";"},"expire_time":-1,"create_time":1582653164,"update_time":1582653164}');

        $response = File::init(self::TEST_CONFIG)->hget('test_hash', 'test_key_1');
        $this->assertFalse($response);
    }

    public function testHgetWrongHash()
    {
        $file_path = File::init(self::TEST_CONFIG)->getCacheFilePath('test_wrong_hash');

        @file_put_contents($file_path, '{"hash":"327d106bf608b1f63bf5cbc5d1b6ea2d6836b446","value":{"test_key_1":"s:17:\"test_val_1\";"},"expire_time":-1,"create_time":1582653164,"update_time":1582653164}');

        $response = File::init(self::TEST_CONFIG)->hget('test_wrong_hash', 'test_key_1');
        $this->assertFalse($response);
    }

    public function testHgetUnknownKey()
    {
        File::init(self::TEST_CONFIG)->del('test_hash');

        $response = File::init(self::TEST_CONFIG)->hget('test_hash', 'test_key_1');
        $this->assertFalse($response);
    }
    
    public function testHdel()
    {
        $response = File::init(self::TEST_CONFIG)->hset('test_new_hash', 'test_key_1', 'test_val_1');
        $this->assertTrue($response);

        $response = File::init(self::TEST_CONFIG)->hset('test_new_hash', 'test_key_2', 'test_val_2');
        $this->assertTrue($response);

        $response = File::init(self::TEST_CONFIG)->hget('test_new_hash', 'test_key_2');
        $this->assertEquals('test_val_2', $response);

        $response = File::init(self::TEST_CONFIG)->hdel('test_new_hash', 'test_key_2');
        $this->assertTrue($response);

        $response = File::init(self::TEST_CONFIG)->hget('test_new_hash', 'test_key_2');
        $this->assertFalse($response);

        $response = File::init(self::TEST_CONFIG)->hdel('test_new_hash', 'test_key_1');
        $this->assertFalse(file_exists(self::TEST_CACHE_DIR . "/te/st/test_new_hash"));
    }

    public function testHdelUnknownHash()
    {
        $response = File::init(self::TEST_CONFIG)->hdel('test_unknown_hash', 'test_key_2');
        $this->assertFalse($response);
    }

    public function testHdelJsonError()
    {
        $file_path = File::init(self::TEST_CONFIG)->getCacheFilePath('test_hash');

        @file_put_contents($file_path, '{hash":"327d106bf608b1f63bf5cbc5d1b6ea2d6836b446","value":{"test_key_1":"s:17:\"test_val_1\";"},"expire_time":-1,"create_time":1582653164,"update_time":1582653164}');

        $response = File::init(self::TEST_CONFIG)->hdel('test_hash', 'test_key_1');
        $this->assertFalse($response);
    }

    public function testHdelWrongHash()
    {
        $file_path = File::init(self::TEST_CONFIG)->getCacheFilePath('test_wrong_hash');

        @file_put_contents($file_path, '{"hash":"327d106bf608b1f63bf5cbc5d1b6ea2d6836b446","value":{"test_key_1":"s:17:\"test_val_1\";","test_key_2":"s:17:\"test_val_2\";"},"expire_time":-1,"create_time":1582653164,"update_time":1582653164}');

        $response = File::init(self::TEST_CONFIG)->hdel('test_wrong_hash', 'test_key_1');
        $this->assertFalse($response);
        $this->assertFalse(file_exists(self::TEST_CACHE_DIR . "/te/st/test_wrong_hash"));
    }

    public function testHmset()
    {
        $response = File::init(self::TEST_CONFIG)->hmset('test_hm_hash', ['test_key_1' => 'test_val_1', 'test_key_2' => 'test_val_2']);
        $this->assertTrue($response);
    }

    public function testHgetall()
    {
        $response = File::init(self::TEST_CONFIG)->hgetall('test_hm_hash');
        $this->assertIsArray($response);
        $this->assertEquals(2, count($response));
        $this->assertTrue($response[0] === 'test_val_1' || $response[1] === 'test_val_1');
    }

    public function testExpire()
    {
        $response = File::init(self::TEST_CONFIG)->expire('test_hm_hash', 1500);
        $this->assertTrue($response);

        $response = File::init(self::TEST_CONFIG)->expire('test_unknown_key', 1500);
        $this->assertFalse($response);
    }

    public function testTtl()
    {
        $response = File::init(self::TEST_CONFIG)->ttl('test_hm_hash');
        $this->assertTrue($response > 0 && $response < 1501);

        $response = File::init(self::TEST_CONFIG)->ttl('test_unknown_hash');
        $this->assertEquals(-2, $response);

        $response = File::init(self::TEST_CONFIG)->set('test_new_key_1', 'test_val', -1);
        $this->assertTrue($response);

        $response = File::init(self::TEST_CONFIG)->ttl('test_new_key_1');
        $this->assertEquals(-1, $response);

        $response = File::init(self::TEST_CONFIG)->set('test_new_key_2', 'test_val', 1);
        $this->assertTrue($response);
        $this->assertTrue(file_exists(self::TEST_CACHE_DIR . "/te/st/test_new_key_2"));
        sleep(2);
        $response = File::init(self::TEST_CONFIG)->ttl('test_new_key_2');
        $this->assertEquals(-2, $response);
        $this->assertFalse(file_exists(self::TEST_CACHE_DIR . "/te/st/test_new_key_2"));
    }

    public function testRemoveDir()
    {
        if (is_dir(self::TEST_CACHE_DIR)) {
            \AAD\Cache\File\Helper::remove(self::TEST_CACHE_DIR);
        }
        $this->assertFalse(is_dir(self::TEST_CACHE_DIR));
    }
}
