<?php

namespace shard;


class ShardingStrategy
{
    protected static $instance = null;
    protected $server1;
    protected $server2;

    protected function __construct() {
        $this->server1 = mysqli_connect('server1', 'user1', 'pass1');
        $this->server2 = mysqli_connect('server2', 'user2', 'pass2');
    }

    public static function getInstance() {
        if (static::$instance == null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function getConnection(Order $order) {
        $server = $this->server1;
        if ($order->user_id % 2 == 0) $server = $this->server2;
        return $server;
    }
}