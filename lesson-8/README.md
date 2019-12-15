> 1. Установить Zabbix Server.
```
sudo rpm -ivh https://repo.zabbix.com/zabbix/4.0/rhel/7/x86_64/zabbix-release-4.0-1.el7.noarch.rpm
sudo yum-config-manager --enable rhel-7-server-optional-rpms
sudo yum install zabbix-server-mysql zabbix-web-mysql
```
Создадим на сервере MySQL новую базу с именем zabbix, а также пользователя zabbix@localhost с полными правами на БД zabbix и зададим ему пароль. Затем развернем дамп:
`zcat /usr/share/doc/zabbix-server-mysql*/create.sql.gz | mysql -uzabbix -p zabbix`

После этого нужно настроить сервер. Конфигурация производится в файле /etc/zabbix/zabbix_server.conf:
```
DBHost=localhost
DBName=zabbix
DBUser=zabbix
DBPassword=zabbix_user_password
```
Подготовим виртуальный хост для сервера мониторинга. Для этого создадим новый файл конфигурации nginx под названием zabbix.local:

```
server {
        listen 80;
        root /usr/share/zabbix;
        access_log /var/log/nginx/zabbix.access.log;
        server_name zabbix.local;
        location / {
                index index.php index.html index.htm;
        }
        location ~ \.php$ {
                fastcgi_pass unix:/var/run/php5-fpm.sock;
                fastcgi_index index.php;
                fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
                include fastcgi_params;
                fastcgi_param PHP_VALUE "
                max_execution_time = 300
                memory_limit = 128M
                post_max_size = 16M
                upload_max_filesize = 2M
                max_input_time = 300
                date.timezone = Europe/Moscow
                always_populate_raw_post_data = -1";
                fastcgi_buffers 8 256k;
                fastcgi_buffer_size 128k;
                fastcgi_intercept_errors on;
                fastcgi_busy_buffers_size 256k;
                fastcgi_temp_file_write_size 256k;
        }
}

```

Также нужно сменить права на файлы логов и логики zabbix:

```
sudo chown -R www-data /etc/zabbix/web
sudo chown -R www-data /usr/share/zabbix
```

Перезапустим nginx. Если все сделано правильно, увидите окно инсталляции.
`sudo systemctl restart nginx`

Ставим zabbix-агент для мониторинга удаленных серверов.

```
 sudo yum install zabbix-agent
 sudo systemctl start zabbix-agent
```

> 2. Добавить шаблон мониторинга HTTP-соединений.

1. Перейдем на вкладку Configuration-Templates - создаем новый шаблон, вводим название шаблона, группу.
2. Открываем этот шаблон. Переходим на вкладку Web Scenarios и добавляем новый сценарий для мониторинга сайта.
Заполняем основные параметры сценария.
После этого перехожу на вкладку Steps и добавляю шаг проверки:

```name index
URL http://172.25.178.174
Required status codes 200
```

После заполнения всех параметров жмем Add, чтобы добавить шаг и далее еще раз Add, чтобы добавить сам сценарий проверки. 

3. Дальше нам надо прикрепить этот шаблон к какому-нибудь хосту, чтобы начались реальные проверки. Я прикреплю шаблон к самому zabbix серверу. Для этого идем в Configuration -> Hosts, выбираем Zabbix Server и прикрепляем к нему созданный ранее шаблон.

Ждем несколько минут и идем в раздел Monitoring -> Web смотреть результаты мониторинга


> 3. Настроить мониторинг созданных в рамках курса виртуальных машин.

Переходим на вкладку Configuration-Hosts-Create host
Вводим данные необходимой мащины:
```
Host Name acloud-mysql-s2
IP address 172.25.178.151
```
Выбираем Groups, переходим на вкладку Templates, выбираем шаблоны, и жмем Add.



> 4.* Добавить шаблон мониторинга NGINX.
