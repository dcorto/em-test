<?php

namespace App\Cache;

use App\Interfaces\ICache;
use Symfony\Component\Cache\Adapter\RedisAdapter;

class Cache implements ICache
{
    const LIFETIME = 5; //Seconds, is low for test only purposes.
    const KEY = 'api.euromillions.results';

    private $redis;

    public function __construct()
    {
        $connection = RedisAdapter::createConnection(
            'redis://localhost:6379', //TODO: Parametrize this
            array(
                'lazy' => false,
                'persistent' => 0,
                'persistent_id' => null,
                'timeout' => 30,
                'read_timeout' => 0,
                'retry_interval' => 0,
            )
        );

        $this->redis = new RedisAdapter(
            $connection,
            '',
            self::LIFETIME
        );
    }

    public function put($json)
    {
        $item = $this->redis->getItem(self::KEY);
        $item->set($json);
        $this->redis->save($item);
    }

    public function get($key)
    {
        return $this->redis->getItem($key)->get();
    }
}