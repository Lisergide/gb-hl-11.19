<?php

namespace app\widgets;


use Redis;

define('REDIS_SERVER', '127.0.0.1');
define('REDIS_PORT', '6379');

/**
 * Class Redis
 * @package app\widgets
 */
class RedisCacheProvider
{

    private $connection = null;

    private function getConnection()
    {
        if ($this->connection === null) {
            $this->connection = new Redis();
            $this->connection->connect(REDIS_SERVER, REDIS_PORT);
        }
        return $this->connection;
    }

    public function get($key)
    {
        $result = false;
        if ($c = $this->getConnection()) {
            $result = unserialize($c->get($key));
        }
        return $result;
    }

    public function set($key, $value, $time = 0)
    {
        if ($c = $this->getConnection()) {
            $c->set($key, serialize($value), $time);
        }
    }

    public function del($key)
    {
        if ($c = $this->getConnection()) {
            $c->delete($key);
        }
    }

    public function clear()
    {
        if ($c = $this->getConnection()) {
            $c->flushDB();
        }
    }
}
