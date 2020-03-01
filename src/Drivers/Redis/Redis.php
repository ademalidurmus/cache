<?php namespace AAD\Cache\Drivers\Redis;

use AAD\Cache\CacheInterface;
use Redis as PHPRedis;

class Redis extends Base implements CacheInterface
{
    protected $connection;

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

    public function hmset(string $hash, array $args)
    {
        foreach ($args as $key => &$value) {
            if (!is_numeric($value)) {
                $value = serialize($value);
            }
        }

        return $this->connection->hmset($hash, $args);
    }
        
    public function flushall()
    {
        return $this->connection->flushall();
    }
}
