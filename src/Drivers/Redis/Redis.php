<?php namespace AAD\Cache\Drivers\Redis;

use AAD\Cache\CacheInterface;
use Redis as PHPRedis;

class Redis implements CacheInterface
{
    private $connection;

    public static function init(PHPRedis $connection)
    {
        $redis = new Redis();
        return $redis->setConnection($connection);
    }

    public function setConnection(PHPRedis $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    public function set(string $key, $value, int $ttl = null)
    {
        if (!is_numeric($value)) {
            $value = serialize($value);
        }

        return $this->connection->set($key, $value, $ttl);
    }

    public function get(string $key)
    {
        $response = $this->connection->get($key);

        if (!is_numeric($response) && !is_bool($response)) {
            $response = unserialize($response);
        }
        
        return $response;
    }

    public function del(string $key)
    {
        return $this->connection->del($key);
    }

    public function hset(string $hash, string $key, $value)
    {
        if (!is_numeric($value)) {
            $value = serialize($value);
        }

        return $this->connection->hset($hash, $key, $value);
    }
    
    public function hget(string $hash, string $key)
    {
        $response = $this->connection->hget($hash, $key);

        if (!is_numeric($response) && !is_bool($response)) {
            $response = unserialize($response);
        }
        
        return $response;
    }

    public function hdel(string $hash, string $key)
    {
        return $this->connection->hdel($hash, $key);
    }

    public function hmset(string $hash, array $args)
    {
        foreach ($args as $key => &$value) {
            if (!is_numeric($value)) {
                $value = serialize($value);
            }
        }

        return $this->connection->hmset($hash, $args);
    }

    public function hgetall(string $hash)
    {
        $response = $this->connection->hgetall($hash);

        if (is_array($response)) {
            foreach ($response as $key => &$value) {
                if (!is_numeric($value)) {
                    $value = unserialize($value);
                }
            }
        }

        return $response;
    }

    public function expire(string $hash, int $ttl)
    {
        return $this->connection->expire($hash, $ttl);
    }

    public function ttl(string $hash)
    {
        return $this->connection->ttl($hash);
    }

    public function exists(string $hash)
    {
        return $this->connection->exists($hash);
    }
    
    public function flushall()
    {
        return $this->connection->flushall();
    }
}
