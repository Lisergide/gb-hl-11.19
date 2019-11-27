> Конфигурация nginx под React + NodeJs:

Создаем каталог под react `mkdir /var/www/myapp`.

Деплоим в нее сбилденное приложение.

Настраиваем nginx.conf
```
    server {
        listen       80 default_server;
        listen       [::]:80 default_server;
        server_name  localhost;
        root	      /var/www/myapp;
        index         index.html index.htm;

        # Load configuration files for the default server block.
        include /etc/nginx/default.d/*.conf;

        location / {
     	    root   /var/www/myapp;
	        index  index.html;

      	    try_files $uri /index.html;
        }
```
Запускаем nginx
```
    systemctl enable nginx
    systemctl start nginx
```

Настраиваем firewalld

```
    firewall-cmd --permanent --zone=public --add-service=ssh
    firewall-cmd --zone=public --add-port=3000/tcp --permanent
    firewall-cmd --zone=public --add-port=8080/tcp --permanent
    firewall-cmd --permanent --zone=public --add-service=http
    firewall-cmd --permanent --zone=public --add-service=https
    firewall-cmd --reload
```

Добавляем юзера

```
    adduser www-data
    gpasswd -a "$USER" www-data
    chown -R "$USER":www-data /var/www
    find /var/www -type f -exec chmod 0660 {} \;
    find /var/www -type d -exec chmod 2770 {} \;
    // после проделанных действий получал 403 ошибку, 
    // пробовал менять разные доступы к папке, в итоге получил 500-ку.
  
    // после гуглешки прописал следующее:
    setsebool -P httpd_can_network_connect on 
    getenforce
    chcon -Rt httpd_sys_content_t /var/www

    // frontend заработал
```

Создаем каталог под nodejs `mkdir /var/www/nodejs/`

Даем права для юзера www-data `chown -R www-data /var/www/nodejs/`

Правим конфиг nginx:
```
location /api {
    	    proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-NginX-Proxy true;
            proxy_pass http://localhost:3034/;
            proxy_ssl_session_reuse off;
            proxy_set_header Host $http_host;
            proxy_cache_bypass $http_upgrade;
            proxy_redirect off;
         }
```

Перезапускаем nginx `systemctl restart nginx`

Открываем порт для nodejs `firewall-cmd --zone=public --add-port=3034/tcp --permanent`

Перезапускаем firewall `firewall-cmd --reload`

ELK ставил через box vagrant `vagrant init zeab/ub-16.04-64x-elk`, поднимаем машину `vagrant up`

Что получаем:
Есть некий TodoList, при добавлении задачи, отправляется запрос на nodejs, который в свою очередь
обращается к logstash.







