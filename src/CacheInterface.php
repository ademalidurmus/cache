<?php

namespace AAD\Cache;

interface CacheInterface
{
    public function set(string $key, $value, int $ttl = null);

    public function get(string $key);

    public function del(string $key);

    public function hset(string $hash, string $key, $value);

    public function hget(string $hash, string $key);

    public function hdel(string $hash, string $key);

    public function hmset(string $hash, array $args);

    public function hgetall(string $hash);

    public function expire(string $hash, int $ttl);

    public function ttl(string $hash);

    public function exists(string $hash);

    public function flushall();
}
