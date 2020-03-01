<?php namespace AAD\Cache\Drivers\Predis;

use AAD\Cache\CacheInterface;
use AAD\Cache\Drivers\Redis\Base as RedisBase;
use Predis\Client as PredisClient;

class Predis extends RedisBase implements CacheInterface
{
    protected $connection;

    public static function init(PredisClient $connection)
    {
        $predis = new Predis();
        return $predis->setConnection($connection);
    }

    public function setConnection(PredisClient $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @var \Predis\Response\Status $response
     * */
    public function set(string $key, $value, int $ttl = null)
    {
        if (!is_numeric($value)) {
            $value = serialize($value);
        }

        if (is_numeric($ttl) && $ttl > -1) {
            if ($ttl === 0) {
                return true;
            }
            $response = $this->connection->setex($key, $ttl, $value);
        } else {
            $response = $this->connection->set($key, $value);
        }
        
        return $response->__toString() === 'OK';
    }

    /**
     * @var \Predis\Response\Status $response
     * */
    public function hmset(string $hash, array $args)
    {
        foreach ($args as $key => &$value) {
            if (!is_numeric($value)) {
                $value = serialize($value);
            }
        }

        $response = $this->connection->hmset($hash, $args);
        return $response->__toString() === 'OK';
    }

    /**
     * @var \Predis\Response\Status $response
     * */
    public function flushall()
    {
        $response = $this->connection->flushall();
        return $response->__toString() === 'OK';
    }
}
