<?php namespace AAD\Cache;

class Cache
{
    private $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public static function init(CacheInterface $cache)
    {
        return new Cache($cache);
    }
    
    public function set(string $key, $value, int $ttl = null)
    {
        $key = self::keyCleaner($key);

        if (!is_null($ttl) && $ttl < -1) {
            $ttl = -1;
        }
        
        return $this->cache->set($key, $value, $ttl);
    }

    public function get(string $key)
    {
        $key = self::keyCleaner($key);

        return $this->cache->get($key);
    }

    public function del(string $key)
    {
        $key = self::keyCleaner($key);

        return $this->cache->del($key);
    }

    public function hset(string $hash, string $key, $value)
    {
        $hash = self::keyCleaner($hash);
        $key = self::keyCleaner($key);

        return $this->cache->hset($hash, $key, $value);
    }

    public function hget(string $hash, string $key)
    {
        $hash = self::keyCleaner($hash);
        $key = self::keyCleaner($key);

        return $this->cache->hget($hash, $key);
    }

    public function hdel(string $hash, string $key)
    {
        $hash = self::keyCleaner($hash);
        $key = self::keyCleaner($key);

        return $this->cache->hdel($hash, $key);
    }

    public function hmset(string $hash, array $args)
    {
        $hash = self::keyCleaner($hash);

        foreach ($args as $key => &$value) {
            $key = self::keyCleaner($key);
        }

        return $this->cache->hmset($hash, $args);
    }

    public function hgetall(string $hash)
    {
        $hash = self::keyCleaner($hash);

        return $this->cache->hgetall($hash);
    }

    public function expire(string $hash, int $ttl)
    {
        $hash = self::keyCleaner($hash);

        if ($ttl < -1) {
            $ttl = -1;
        }

        return $this->cache->expire($hash, $ttl);
    }

    public function ttl(string $hash)
    {
        $hash = self::keyCleaner($hash);

        return $this->cache->ttl($hash);
    }

    private static function keyCleaner(string $key)
    {
        return preg_replace('/[^A-Za-z0-9]/', '_', $key);
    }
}
