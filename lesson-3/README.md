#### Урок 3. Оптимизация БД
> 1. Установить выбранный форк MySQL Server.

##### Установка MySQL (MariaDB)
```
    sudo yum install mariadb-server mariadb -y 
    sudo systemctl start mariadb 
    sudo mysql_secure_installation
    sudo systemctl enable mariadb.service

    // открываем 3306 порт для внешнего подключения
    sudo firewall-cmd --zone=public --add-port=3306/tcp --permanent
    sudo firewall-cmd --reload
```
> 2. Портировать в него текущую структуру таблиц.

##### Создаем БД

```
    sudo mysql -u root mysql -p
    create database mydb;
    // даем внешний доступ к серверу 
    GRANT ALL ON *.* to mysql@'ip-addr' IDENTIFIED BY 'password';
    FLUSH PRIVILEGES;

    // импортируем бд
    sudo mysql -u root -p mydb < explain_models.sql
```

##### Наблюдение за БД

Отправим несколько запросов, и посмотрим на дашборд приложения MySQL Workbench 
```
use mydb;

SELECT * FROM `orders`;
```
На вкладке Dashboard можем наблюдать за состоянием сервера:

![img 1](https://github.com/Lisergide/gb-hl-11.19/blob/master/lesson-3/img/1.jpg)

> 3. Какие ситуации, вызывающие рост количества запросов, могут случаться на сервере? Мы рассмотрели не все.

- наличие уязвимостей(хакерские атаки, атаки ботов);
- спам через незащищенные формы;
- неоптимизированные скрипты;

> 4.* В каких случаях индекс в MySQL не будет применятся, даже если он доступен и выборка должна использовать его?

 - Если использование индекса требует от MySQL прохода более чем по 30% строк в данной таблице 
   (в таких случаях просмотр таблицы, по всей видимости, окажется намного быстрее, так как потребуется выполнить меньше
   операций поиска). Следует учитывать, что если подобный запрос использует LIMIT по отношению только к извлекаемой части 
   строк, то MySQL будет применять индекс в любом случае, так как небольшое количество строк можно найти намного быстрее, 
   чтобы вернуть результат.
 
 - Если диапазон изменения индекса может содержать величины NULL при использовании выражений ORDER BY ... DESC.
 
 > 5.* Как принудительно применить индекс?

`SELECT * FROM table1 USE INDEX (col1_index,col2_index)
   WHERE col1=1 AND col2=2 AND col3=3;`

