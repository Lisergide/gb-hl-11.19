> 1. Построить NGINX-балансировку между двумя виртуальными машинами. Доказать, что балансировка происходит.

Собираем 2-ю виртуальную машину, которая будет полной копией первой.

В папке настроек `NGINX` создадим новый файл `upstream.conf`:

```
upstream backend {
  server backend1.mysite.local:8080;
  server backend2.mysite.local:8080;
}

server {
  listen 80;
  server_name mysite.local;

  location / {
    proxy_pass http://backend;
  }
} 
```
На каждой машине кластера оставляем стандартный файл конфигурации, который использовали с первого урока, 
но внесем в него два изменения:

```
    # указываем 8080 порт для соединения
    listen 8080;
    # нужно указать какому доменному имени принадлежит конфиг
    server_name backend1.mysite.local;
```

Саму балансировку будем производить методом `Round Robin`;

Для проверки балансировки можно воспользоваться утилитой `siege`:
```
sudo yum install siege

siege -b -c10 -t30S http://mysite.local/hello/
```
![img 1](https://github.com/Lisergide/gb-hl-11.19/blob/lesson-6/lesson-6/img/1.jpg)

> 2. Реализовать альтернативное хранение сессий в Memcached.

Чтобы сессии хранились в Memcached, нужно отредактировать php.ini:

```
session.save_handler = memcache
session.save_path = "tcp://mysite.local:11211"
```

> 3. Настроить NGINX для работы с символьной ссылкой:

`ln —s /usr/share/nginx/www/mysite-a.local mysite-prod.local`

Переключим ссылку на другую директорию:

`ln —s /usr/share/nginx/www/mysite-b.local mysite-prod.local`

Теперь ссылка смотрит на `mysite-b.local`. `NGINX` при этом все так же считает домашней директорию `mysite-prod.local`.