# Cache
PHP cache library with PSR-16

[![Build Status](https://travis-ci.com/AdemAliDurmus/cache.svg?branch=master)](https://travis-ci.com/AdemAliDurmus/cache)
[![Latest Stable Version](https://poser.pugx.org/aad/cache/v/stable)](https://packagist.org/packages/aad/cache)
[![Total Downloads](https://poser.pugx.org/aad/cache/downloads)](https://packagist.org/packages/aad/cache)
[![License](https://poser.pugx.org/aad/cache/license)](https://packagist.org/packages/aad/cache)

## Supported Drivers
- Files
- Redis

## Installation
```
composer require aad/cache
```

## Usage
```php
use AAD\Cache\Cache;
use AAD\Cache\Drivers\Files\Files;
use AAD\Cache\Drivers\Redis\Redis;
use AAD\Cache\Drivers\Predis\Predis;

// for using files driver
$config = [
    'cache_dir' => __DIR__ . '/_cache_files_test', // cache file directory
    'cache_ttl' => 180, // set cache ttl
];
$driver = Files::init($config);

// for using redis driver
$connection = new \Redis();
$connection->connect('localhost', 6379);
$driver = Redis::init($connection);

// for using predis driver
$connection = new \Predis\Client();
$connection->connect('localhost', 6379);
$driver = Predis::init($connection);

$cache = new Cache($driver);

$cache->set('test_key', 'test_val'); // set cache with specific key
$cache->get('test_key', 'default_val'); // get cache value for specific key, if the key does not exist, you can return a default value
$cache->ttl('test_key'); // get key ttl
$cache->del('test_key'); // delete key
$cache->delete('test_key'); // delete key
$cache->hset('test_new_key', 'key', 'val'); // set hash value for specific key
$cache->hget('test_new_key', 'key'); // get hash value for specific key
$cache->hdel('test_new_key', 'key'); // delete hash value for specific key
$cache->expire('test_new_key', 10); // set ttl
$cache->hmset('test_hash_key', ['key_1' => 'val_1', 'key_2' => 'val_2']); // set cache for spesfic hash with key value pairs
$cache->hgetall('test_hash_key'); // get all values for spesfic hash
$cache->exists('test_hash_key'); // check key is exist
$cache->has('test_hash_key'); // check key is exist
$cache->flushall(); // delete all cache
$cache->clear(); // delete all cache
$cache->setMultiple(['key_1' => 'val_1', 'key_2' => 'val_2'], 10); // set cache for multiple key value pairs with ttl
$cache->getMultiple(['key_1', 'key_2', 'key_3'], 'default val'); // get cache for multiple keys, if some keys does not exist, you can return a default value
$cache->deleteMultiple(['key_1' => 'val_1', 'key_2' => 'val_2']); // delete cache for multiple key value pairs
```