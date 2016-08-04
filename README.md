# Тестовое задание webinar.ru  

Сделано на laravel 5.  


# app/Http/routes.php   
Файл Маршрутизации, где задаются маршруты для api.  
* GET /broadcast Получение списка активных трансляций   
* POST /broadcast Добавление новой трансляции   
* GET /broadcast/{id} Получение информации о трансляции по ID   
* PUT/PATCH /broadcast/{id} Изменение существующей трансляции   
* DELETE /broadcast/{id} Удаление трансляции   

https://laravel.com/docs/5.1/controllers#restful-resource-controllers  

# database/migrations/2016_08_04_062726_create_broadcasts_table.php  
Миграция для создания таблицы трансляций broadcasts в базе данных  
'id' //Первичный ключ ID  
'name' //Название  
'description' //Описание  
'leader' //Имя ведущего  
'status' //Статус (Не начата, Идет, Закончена)  
'started_at' //Время начала трансляции  
'finished_at' //Время конца  

# app/Broadcast.php  
Модель для таблицы трансляций broadcasts 

# app/Http/Controllers/BroadcastController.php  
Контроллер для обработки трансляций.  
В laravel 5 при возвращении массива или объекта, он преобразуется в json  
В конструкторе контроллера в ручную переопределятся request'ы для валидации данных для методов изменения и создания трансляций.  

# Методы контроллера  
Получение списка активных трансляций: index  
Получение информации о трансляции по ID: show  
Добавление новой трансляции: store  
Изменение существующей трансляции: update  
Запуск трансляции: update  
Остановка трансляции: update  
Удаление трансляции: destroy  
Метод авторизации: в app/Http/Kernel.php подключается авторизация фреймворка. Страница авторизации /login  

Для запуска трансляции, постановки на паузу или окончания трансляции нужно у модели Broadcast поменять статус.  

# Redis  
В редис записываю json строку  
По ключу broadcasts список всех трансляций  
По маске ключей broadcast:* по первичному ключу модель трансляции  

# app/Http/Requests/BroadcastStoreRequest.php  
Валидация создания трансляции   

# app/Http/Requests/BroadcastUpdateRequest.php  
Валидация изменения трансляции  

# tests/BroadcastTest.php  
Тесты api  

# Комментарии к заданию  
Для работы приложения может понадобиться поменять флаг 'default' с pgsql на mysql в конфиге app/config/database.php  
