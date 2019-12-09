<?php
// создаём инстанс
$memInst = new Memcached();

// подключаемся к серверу
$memInst->addServer('localhost', 11211);

// добавляем переменные в кэш
// первое значение – имя ключа, второе - значение
$memInst->set('int', 13);
$memInst->set('string', 'Test');
$memInst->set('array', array(11, 12));

// здесь мы указываем время хранения записи с ключом 'object' - 5 минут
$memInst->set('object', new stdclass, time() + 300);

// теперь мы можем вытаскивать значения прямо из кэша
var_dump($memInst->get('int'));
var_dump($memInst->get('string'));
var_dump($memInst->get('array'));
var_dump($memInst->get('object'));
