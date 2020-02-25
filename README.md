# Cache
PHP cache library

[![Build Status](https://travis-ci.com/AdemAliDurmus/cache.svg?branch=master)](https://travis-ci.com/AdemAliDurmus/cache)
[![Latest Stable Version](https://poser.pugx.org/aad/cache/v/stable)](https://packagist.org/packages/aad/cache)
[![Total Downloads](https://poser.pugx.org/aad/cache/downloads)](https://packagist.org/packages/aad/cache)
[![Latest Unstable Version](https://poser.pugx.org/aad/cache/v/unstable)](https://packagist.org/packages/aad/cache)
[![License](https://poser.pugx.org/aad/cache/license)](https://packagist.org/packages/aad/cache)

## Supported Drivers
- Files
- Redis (WIP)

## Installation
```
composer require aad/cache
```

## Usage
```php
use AAD\Cache\Cache;
use AAD\Cache\File;

$config = [
    'cache_dir' => __DIR__ . '/tmp', // cache file directory
    'cache_ttl' => 180, // set cache ttl
];

$file = File::init($config);
$cache = new Cache($file);

$cache->set('test_key', 'test_val'); // set cache with specific key
$cache->get('test_key'); // get cache value for specific key
$cache->ttl('test_key'); // get key ttl
$cache->del('test_key'); // delete key
$cache->hset('test_new_key', 'key', 'val'); // set hash value for specific key
$cache->hget('test_new_key', 'key'); // get hash value for specific key
$cache->hdel('test_new_key', 'key'); // delete hash value for specific key
$cache->expire('test_new_key', 10); // set ttl
$cache->hmset('test_hash_key', ['key_1' => 'val_1', 'key_2' => 'val_2']); // set cache for spesfic hash with key value pairs
$cache->hgetall('test_hash_key'); // get all values for spesfic hash
```