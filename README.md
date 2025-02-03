# API сервиса по сокращению ссылок

>Laravel v10.26.2
>
>PHP 8.1
>
>Spattie Permissions
>
>L5-Swagger Documentation
>
>Разделение проекта на модули (App -> Modules, ModuleServiceProvider)
>
>Унифицированный CRUD репозиторий
>
>Кэширование GET запросов (GlobalObserverProvider & CacheObserver)
>
>Автозаполнение таблицы прав из контроллеров
>
>CRUD & Permissions traits
>
>Кастомные ошибки
>
>Вся логика вынесена из контроллеров в сервисы
>
>Google & Facebook auth
>
>Gitlab CI/CD

Docker-контейнеры:
* local
* testing
* stage

Local: Настроен на локальный запуск по адресу http://localhost:8000

Документация: http://"site url"/api/docs

## Неообходимо

* Docker

## Как тут сделан OAuth

Переход по */auth/google/redirect возвращает ссылку на вход, на которую нужно редиректнуть. Например: {"
redirect_url": "https://auth.provider.url"}

После авторизации на сайте (https://auth.provider.url), происходит редирект на */auth/{provider}/callback, 
где возвращается token авторизации.

## Запуск

Скопировать проект в папку, создать .env файл:
```shell
cp .env.example .env
```
Настроить .env файл. Необходимые поля:
* APP_FRONT_URL
* ADMIN_EMAIL
* ADMIN_PWD
* BASIC_USER_EMAIL
* BASIC_USER_PWD
* GOOGLE_CLIENT_ID
* GOOGLE_CLIENT_SECRET
* FACEBOOK_CLIENT_ID
* FACEBOOK_CLIENT_SECRET
* Поля MAIL'ера

Запустить контейнеры локально:
```shell
docker compose -f ./docker/local/docker-compose.yml up -d
```

По умолчанию в контейнере php-queue в docker-compose указан entrypoint на следующие команды при запуске:
```shell
composer update

php artisan key:generate

php artisan optimize:clear

php artisan optimize

php artisan migrate:fresh --force

php artisan db:seed --force

php artisan queue:work
```

## После запуска необходимо выдать права обычным пользователям (/permissions/sync)
Приблизительный список необходимых для работы прав:
```
7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19
```

## Дополнительно

По умолчанию каждый запуск стирает БД. Для сохранения данных после первого запуска в ./local/docker-compose.yml заменить:
```shell
command: [ "./docker/entrypoint.sh" ]
```

На:
```shell
command: php artisan queue:work
```
