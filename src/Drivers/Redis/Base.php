<?php namespace AAD\Cache\Drivers\Redis;

class Base
{
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
}
