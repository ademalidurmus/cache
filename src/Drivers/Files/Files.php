<?php

namespace AAD\Cache\Drivers\Files;

use AAD\Cache\CacheInterface;
use AAD\Cache\Drivers\Files\Helper;
use Ramsey\Uuid\Uuid;

class Files implements CacheInterface
{
    private $cache_dir = __DIR__ . "/../../../_cache_files";
    private $cache_ttl = -1;

    public static function init(array $config = [])
    {
        $files = new Files();
        return $files->setConfig($config);
    }

    public function setConfig(array $config = [])
    {
        foreach ($config as $key => $value) {
            switch ($key) {
                case 'cache_dir':
                    if (!empty($value)) {
                        $this->setCacheDir($value);
                    }
                    break;

                case 'cache_ttl':
                    if (is_numeric($value) && $value > 0) {
                        $this->cache_ttl = $value;
                    }
                    break;
            }
        }

        return $this;
    }

    public function setCacheDir(string $dir)
    {
        $this->cache_dir = $dir;
        $this->getCacheDir();
        return $this;
    }

    public function getCacheDir(string $hash = null)
    {
        Helper::mkdir($this->cache_dir);

        if (!is_null($hash)) {
            $cache_dir = $this->cache_dir;
            $cache_dir = rtrim($cache_dir, "/");

            if (mb_strlen($hash) >= 2) {
                $cache_dir .= "/" . mb_substr($hash, 0, 2);
                Helper::mkdir($cache_dir);
            }

            if (mb_strlen($hash) >= 4) {
                $cache_dir .= "/" . mb_substr($hash, 2, 2);
                Helper::mkdir($cache_dir);
            }

            return $cache_dir;
        }

        return $this->cache_dir;
    }

    public function getCacheFilePath(string $hash)
    {
        $hash = Uuid::uuid5(Uuid::NIL, $hash);
        return $this->getCacheDir($hash) . "/{$hash}";
    }

    public function set(string $key, $value, int $ttl = null)
    {
        $path = $this->getCacheFilePath($key);

        if (is_null($ttl)) {
            $ttl = $this->cache_ttl;
        }

        $value = serialize($value);
        $data = [
            'key' => $key,
            'value' => $value,
            'expire_time' => $ttl == -1 ? -1 : time() + $ttl,
            'create_time' => time(),
            'update_time' => time(),
            'hash' => sha1("{$key}{$value}"),
            'method' => 'set',
        ];

        if (file_exists($path)) {
            $current_data = json_decode(file_get_contents($path), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if ($current_data['method'] !== 'set') {
                    return false;
                }
                $data['create_time'] = $current_data['create_time'] ?? time();
            }
        }

        return Helper::put($path, json_encode($data));
    }

    public function get(string $key)
    {
        $path = $this->getCacheFilePath($key);
        $data = $this->getCacheContent($path);

        if ($data === false) {
            return false;
        }

        if (sha1("{$key}{$data['value']}") !== $data['hash'] || ($data['expire_time'] !== -1 && $data['expire_time'] < time())) {
            @unlink($path);
            return false;
        }

        return unserialize($data['value']);
    }

    public function del(string $key)
    {
        $path = $this->getCacheFilePath($key);

        if (!file_exists($path)) {
            return false;
        }

        @unlink($path);

        return true;
    }

    public function hset(string $hash, string $key, $value)
    {
        return $this->hashSet($hash, [$key => $value]);
    }

    public function hget(string $hash, string $key)
    {
        $response = false;
        $data = $this->hashGet($hash, [$key]);
        if (is_array($data) && count($data) === 1) {
            $response = $data[0];
        }

        return $response;
    }

    public function hdel(string $hash, string $key)
    {
        return $this->hashDel($hash, [$key]);
    }

    public function hmset(string $hash, array $args)
    {
        return $this->hashSet($hash, $args);
    }

    public function hgetall(string $hash)
    {
        return $this->hashGet($hash, [], true);
    }

    public function expire(string $hash, int $ttl)
    {
        $path = $this->getCacheFilePath($hash);
        $data = $this->getCacheContent($path);

        if ($data === false) {
            return false;
        }

        $data['update_time'] = time();
        $data['expire_time'] = $ttl == -1 ? -1 : time() + $ttl;

        return Helper::put($path, json_encode($data));
    }

    public function ttl(string $hash)
    {
        $path = $this->getCacheFilePath($hash);
        $data = $this->getCacheContent($path);

        if ($data === false) {
            return -2;
        }

        if ($data['expire_time'] === -1) {
            return -1;
        }

        $ttl = $data['expire_time'] - time();
        if ($ttl < 1) {
            @unlink($path);
            return -2;
        }

        return $ttl;
    }

    public function exists(string $hash)
    {
        $path = $this->getCacheFilePath($hash);
        $data = $this->getCacheContent($path);

        if ($data === false) {
            return false;
        }

        if ($data['expire_time'] !== -1 && $data['expire_time'] < time()) {
            @unlink($path);
            return false;
        }

        return true;
    }

    public function flushall()
    {
        Helper::remove($this->cache_dir);
        return !is_dir($this->cache_dir);
    }

    private function hashSet(string $hash, array $content)
    {
        $path = $this->getCacheFilePath($hash);

        foreach ($content as $key => &$value) {
            $value = serialize($value);
        }

        $data = [
            'key' => $hash,
            'value' => $content,
            'expire_time' => -1,
            'create_time' => time(),
            'update_time' => time(),
            'hash' => sha1($hash),
            'method' => 'hset',
        ];

        if (file_exists($path)) {
            $current_data = json_decode(file_get_contents($path), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                if ($current_data['method'] !== 'hset') {
                    return false;
                }
                $data['create_time'] = $current_data['create_time'] ?? time();
                if (is_array($current_data['value'])) {
                    $data['value'] = array_merge($current_data['value'], $data['value']);
                }
            }
        }

        return Helper::put($path, json_encode($data));
    }

    private function hashGet(string $hash, array $keys, bool $all = false)
    {
        $path = $this->getCacheFilePath($hash);
        $data = $this->getCacheContent($path);

        if ($data === false) {
            return false;
        }

        if (sha1($hash) !== $data['hash'] || ($data['expire_time'] !== -1 && $data['expire_time'] < time())) {
            @unlink($path);
            return false;
        }

        $return = [];

        foreach ($data['value'] as $key => $value) {
            if (in_array($key, $keys) || $all) {
                $return[] = unserialize($value);
            }
        }

        return $return;
    }

    private function hashDel(string $hash, array $keys)
    {
        $path = $this->getCacheFilePath($hash);
        $data = $this->getCacheContent($path);

        if ($data === false) {
            return false;
        }

        if ($data['method'] !== 'hset') {
            return false;
        }

        if (sha1($hash) !== $data['hash'] || ($data['expire_time'] !== -1 && $data['expire_time'] < time())) {
            @unlink($path);
            return false;
        }

        foreach ($data['value'] as $key => $value) {
            if (in_array($key, $keys)) {
                unset($data['value'][$key]);
            }
        }

        if (count($data['value']) === 0) {
            @unlink($path);
            return true;
        }

        $data['update_time'] = time();

        return Helper::put($path, json_encode($data));
    }

    public function getCacheContent(string $path)
    {
        if (!file_exists($path)) {
            return false;
        }

        $data = json_decode(file_get_contents($path), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        return $data;
    }
}
