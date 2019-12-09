<?php
namespace shard;
// подгрузка всех настроек приложения
$list = array_merge(
    include 'OrderStorage.php',
    include 'Order.php',
    include 'ShardingStrategy.php',
);