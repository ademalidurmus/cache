<?php namespace AAD\Cache;

use Psr\SimpleCache\CacheInterface as SimpleCacheInterface;

class Cache implements SimpleCacheInterface
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
    
    public function set($key, $value, $ttl = null)
    {
        $key = self::keyCleaner($key);

        if (!is_null($ttl) && $ttl < -1) {
            $ttl = -1;
        }
        
        return $this->cache->set($key, $value, $ttl);
    }

    public function get($key, $default = false)
    {
        $key = self::keyCleaner($key);
        $value = $this->cache->get($key);

        if ($value === false) {
            return $default;
        }

        return $value;
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

    public function exists(string $hash)
    {
        $hash = self::keyCleaner($hash);

        return $this->cache->exists($hash);
    }

    public function flushall()
    {
        return $this->cache->flushall();
    }

    private static function keyCleaner(string $key)
    {
        return preg_replace('/[^A-Za-z0-9]/', '_', $key);
    }

    public function delete($key)
    {
        $key = self::keyCleaner($key);

        return $this->cache->del($key);
    }

    public function clear()
    {
        return $this->flushall();
    }

    public function getMultiple($keys, $default = false)
    {
        $data = [];

        foreach ($keys as $key) {
            $key = self::keyCleaner($key);

            $value = $this->cache->get($key);
            
            if ($value === false) {
                $value = $default;
            }

            $data[$key] = $value;
        }

        return $data;
    }

    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $key = self::keyCleaner($key);

            if (!is_null($ttl) && $ttl < -1) {
                $ttl = -1;
            }

            $response = $this->cache->set($key, $value, $ttl);

            if ($response === false) {
                return false;
            }
        }

        return true;
    }

    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $key = self::keyCleaner($key);

            $response = $this->cache->del($key);

            if ($response === false) {
                return false;
            }
        }

        return true;
    }

    public function has($key)
    {
        return $this->exists($key);
    }
}
