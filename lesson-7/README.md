> 1. Установить RabbitMQ.
Устанавливаем RabbitMQ:
```
sudo yum install rabbitmq-server
sudo rabbitmq-plugins enable rabbitmq_management
sudo service rabbitmq-server restart
```
При установке RabbitMQ автоматически создает пользователя guest:guest. Его можно использовать исключительно при 
соединении с локальной машины. Создадим еще одного пользователя и выдадим ему права администратора:
```
rabbitmqctl add_user test test
rabbitmqctl set_user_tags test administrator
rabbitmqctl set_permissions -p / test ".*" ".*" ".*"
``` 

Интерфейс будет доступен по адресу: `http://172.25.178.174:15672/`

![img 1](https://github.com/Lisergide/gb-hl-11.19/blob/lesson-7/lesson-7/img/1.jpg)

> 2. Создать несколько очередей.
Установим PHP-расширение для работы с RabbitMQ.
```
sudo yum install php-amqp
```
Создадим пару очередей на заказ `Coffee` и на заказ `Pizza`, скрипт обработки формы находится в директории:
`./www/app/TestController.php`;

> 3. Реализовать цепочку «Заказ еды — оплата — доставка — отзыв клиента». Сколько понадобится очередей?
4 очереди (Order - Payment - Delivery - Feedback).

