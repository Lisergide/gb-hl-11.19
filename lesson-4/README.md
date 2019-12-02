> 1. Собрать две виртуальные машины с установленным MySQL-сервером.

Первая машина у нас уже есть, создаем вторую:

Устанавливаем MySQL (MariaDB)

```
 sudo yum install mariadb-server mariadb -y 
 sudo systemctl start mariadb
 sudo mysql_secure_installation
 sudo systemctl enable mariadb.service
```

> 2. Развернуть репликацию на этих двух серверах.

Посмотрим информацию о сетевых интерфейсах, у **master**  будет адрес `172.25.178.104`, а у **slave** - `172.25.178.151`

Настраиваем **master** . В файл `/etc/my.cnf` добавим строки:
```
sudo nano /etc/my.cnf

//Добавляем следующие строки:
[mariadb]
server-id=1
log_bin=mysql-bin.log
binlog_do_db=mydb
```
Перезапускаем **master**: `systemctl restart mariadb.service`

На **master** создаем пользователя, который будет отвечать за репликацию:
```
GRANT REPLICATION SLAVE ON *.* TO 'slave_user'@'%' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
```
Теперь нам необходимо предотвратить любые изменения данных для того чтобы получить текущую позицию в binary log.
Это необходимо для того, чтобы задать всем slave позицию начала репликации данных.

На **master**, заблокируем все таблицы выполнив `FLUSH TABLES WITH READ LOCK;`

Получаем текущую позицию binary log выполнив `SHOW MASTER STATUS;`:

![img 1](https://github.com/Lisergide/gb-hl-11.19/blob/master/lesson-4/img/1.jpg)

Делаем дам мастера БД, после чего разблокируем ее:
```
sudo mysqldump -u root -p mydb > dump.sql
UNLOCK TABLES;
```
Создаем БД на **slave** с таким же именем, как на **master**:
```
sudo mysql -u root mysql -p
create database mydb;

// Заливаем дамп из мастера на реплику.
sudo mysql -u root -p mydb < dump.sql
```
Настраиваем реплику на **slave** в файле `/etc/my.cnf`.
```
sudo nano /etc/my.cnf

//Добавляем следующие строки:
[mariadb]
server-id=2
relay-log=mysql-relay-bin.log
log_bin=mysql-bin.log
replicate_do_db=mydb
```
Перезагружаем **slave** сервер.

Включаем репликацию, указывая данные из таблицы `SHOW MASTER STATUS;`:
```
CHANGE MASTER TO MASTER_HOST='172.25.178.104',
MASTER_USER='slave_user',
MASTER_PASSWORD='password',
MASTER_LOG_FILE='mysql-bin.000001',
MASTER_LOG_POS=245;

START SLAVE;
```
Проверяем работу репликации запросом `SHOW SLAVE STATUS\G;`:

![img 2](https://github.com/Lisergide/gb-hl-11.19/blob/master/lesson-4/img/2.jpg)


