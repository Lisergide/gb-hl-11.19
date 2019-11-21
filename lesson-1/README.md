> Развернуть на виртуальной машине выбранную сборку.

Разворачивал сборку `Nginx + php + MySQL` на ОС `CentOs 7`

##### Установка Nginx 
```
yum install epel-release -y

yum install nginx -y

```
######  Открываем порты 80 и 443.
```
firewall-cmd --permanent --zone=public --add-service=http 

firewall-cmd --permanent --zone=public --add-service=https 

firewall-cmd --reload
```

###### Запускаем Nginx
```
systemctl start nginx.service
systemctl enable nginx.service 
```
##### Установка MySQL (MariaDB)

```
yum install mariadb-server mariadb -y 
systemctl start mariadb 
mysql_secure_installation
systemctl enable mariadb.service
```

##### Установка и настройка PHP
```
yum install php php-mysql php-fpm -y 
```
Открываем файл `sudo nano /etc/php.ini` дописываем строку: `cgi.fix_pathinfo=0`

Редактируйте файл `nano /etc/php-fpm.d/www.conf`, меняем значение `listen=` на `listen = /var/run/php-fpm/php-fpm.sock`

Запускаем `php-fpm`:

```
systemctl start php-fpm
systemctl enable php-fpm.service
```

##### Настройка Nginx 

Открываем файл: `nano /etc/nginx/conf.d/default.conf`

```
server {
    listen    80;
    server_name  172.25.178.174;
    charset utf-8;
    root   /usr/share/nginx/html;
    index index.php index.html index.htm;
        location / {
            try_files $uri $uri/ =404;
        } error_page 404 /404.html;
    error_page 500 502 503 504 /50x.html;
    location = /50x.html {
        root /usr/share/nginx/html;
    }
    location ~ \.php$ {
    try_files $uri =404;
    fastcgi_pass unix:/var/run/php-fpm/php-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
    }
}
```

Перезапускаем Nginx `systemctl restart nginx`

> Попробовать создать несколько PHP-скриптов в системе.

Создадим файл для проверки работы сборки `nano /usr/share/nginx/html/info.php` :
```php
<?php phpinfo(); ?>
```

Выполним несколько скриптов на php:
`nano /usr/share/nginx/html/hello.php`

```php
<?php
    echo "Hello, World!";
    echo "\n<br><br>";

    $name = "GeekBrains user";
    echo "Hello, $name!";
    echo "\n<br><br>";

    define('MY_CONST', 100);
    echo MY_CONST;
    echo "\n<br><br>";

    $int10 = 42;
    $int2 = 0b101010;
    $int8 = 052;
    $int16 = 0x2A;
    echo "Десятеричная система $int10 <br>";
    echo "Двоичная система $int2 <br>";
    echo "Восьмеричная система $int8 <br>";
    echo "Шестнадцатеричная система $int16 <br>";
    echo "\n<br><br>";

    $precise1 = 1.5;
    $precise2 = 1.5e4;
    $precise3 = 6E-8;
    echo "$precise1 | $precise2 | $precise3";
    echo "\n<br><br>";
```

И второй скрипт выводящий на экран текущее время:
`nano /usr/share/nginx/html/time.php`

```php
<?php
function getNowTime() {
  $time = time();
  $hours = date('H', $time);
  $minutes = date('i', $time);

  return $hours . ' ' . getCorrectVariant($hours, 'час', 'часа', 'часов') . ' '
    . $minutes . ' ' . getCorrectVariant($minutes, 'минута', 'минуты', 'минут');
}
function getCorrectVariant($num, $var1, $var2, $var3) {
  if ($num > 20) {
    $num %= 10;
  }
  if ($num > 1 && $num < 5) {
    return $var2;
  } elseif ($num === 1) {
    return $var1;
  } else {
    return $var3;
  }
}
echo getNowTime(), '<br>';
```


