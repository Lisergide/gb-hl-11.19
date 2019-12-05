> 1. Развернуть механизмы кеширования на виртуальной машине.

Добавим механизм кэширование через `NGINX` на ВМ, добавим конфигурацию в `sudo nano /etc/nginx/nginx.conf`, будем хранить кешированный контент 1 неделю:
```
# при обращении к статическим файлам не нужны логи и обращение к fpm
location ~* .(jpg|jpeg|gif|css|png|js|ico|html)$ {
    access_log off;
    expires 7d;
}
```
Также будем передавать в браузер заголовок Cache-Control:
```
# Отключаем кеширование на клиенте
add_header Cache-Control: no-cache, no-store, must-revalidate
```
В блок вызова FPM добавим следующие директивы:
```
location ~ \.php$ {
    try_files $uri =404;
    fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
    # Отключаем кеширование на клиенте
    fastcgi_hide_header "Cache-Control";
    fastcgi_hide_header "Expires";
    fastcgi_ignore_headers "Cache-Control" "Expires";
}
```
> 2. Реализовать схему загрузки данных из Redis (без Memcached в NGINX) согласно иллюстрации в главе «Что выбрать». Замерить производительность страницы без кеша и с ним.

Реализация находится в каталоге `lesson-5/www`;

> 3.* Подключить Memcached к NGINX. Показать, что страница была загружена из кеша (или нет).

Устанавливаем memcached:

```
sudo yum install memcached php72-php-pecl-memcached
sudo service php-fpm restart
```

Проверяем, что все запустилось:
```
netstat -tap | grep memcached
php-fpm —m | grep memcached
```

Создадим объект класса Memcahed `sudo nano /usr/share/nginx/www/memcached.php`:

```
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
```

Подключаем модуль `memcached` в `nginx config`, добавим к location / {}:

```
    set            $memcached_key "$uri?$args";
    memcached_pass host:11211;
```

> 4.* Исправить код класса-обертки для Redis.

Класс находится в директории `www/redis.php`;

