# 🔗 LinkCrop Laravel API

[![Laravel](https://img.shields.io/badge/Laravel-10.x-ff2d20.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1-777bb4.svg)](https://php.net)
[![Swagger](https://img.shields.io/badge/Swagger-L5-85ea2d.svg)](https://swagger.io)
[![Docker](https://img.shields.io/badge/Docker-Supported-blue.svg)](https://www.docker.com)

REST API для сервиса сокращения ссылок с модульной архитектурой, кэшированием и системой прав доступа.

## 📌 О проекте

Бэкенд-часть сервиса LinkCrop — API для управления сокращёнными ссылками. Архитектура построена по модульному принципу с выносом логики в сервисы. Поддерживает аутентификацию через Google и Facebook, документирован через Swagger/OpenAPI.

### 🌟 Ключевые особенности
*   🧩 **Модульная архитектура** — разделение на `Auth`, `Core`, `Links`, `Users`
*   🔐 **RBAC (Role-Based Access Control)** — Spatie Permissions
*   📝 **Автоматическая документация** — L5-Swagger (/api/docs)
*   ⚡ **Умное кэширование** — кэширование GET-запросов через кастомные Middleware/Observer
*   🔄 **Унифицированный CRUD** — базовый репозиторий для всех сущностей
*   🚀 **OAuth 2.0** — вход через Google и Facebook
*   🤖 **Gitlab CI/CD** — пайплайн для автоматизации

## 🛠️ Технологический стек

| Технология         | Версия   | Назначение                      |
|:-------------------|:---------| :------------------------------ |
| Laravel            | v10.26.2 | Основной фреймворк              |
| PHP                | 8.1      | Язык программирования           |
| PostgreSQL         | -        | База данных                     |
| Redis              | -        | Кэширование
| Spatie Permissions | ^5       | Управление ролями и правами     |
| L5-Swagger         | ^8       | Документация API                |
| Docker             | -        | Контейнеризация                 |

## 📁 Архитектура и структура

Проект разделён на модули, расположенные в `app/Modules/`:
```
app/Modules/
├── Auth/ # Аутентификация (OAuth, регистрация, сброс пароля)
├── Core/ # Ядро (базовые классы, трейты, хелперы, Middleware)
├── Users/ # Пользователи
└── Links/ # Основной модуль управления ссылками и группами
```

## 🚀 Быстрый старт

### Предварительные требования
*   Docker и Docker Compose
*   Свободные порты: `8000` (API), `5432` (PostgreSQL)

### Запуск через Docker
1.  Склонировать репозиторий, создать .env и заполнить полями и пункта "Переменные окружения":
    ```
    cp .env.example .env
    ```
2.  Запустить контейнеры
    ```
    docker compose -f ./docker/local/docker-compose.yml up -d
    ```

После запуска API будет доступен по адресу http://localhost:8000.
Документация Swagger: http://localhost:8000/api/docs

### 🔧 Первоначальная настройка прав
После первого запуска и миграций необходимо синхронизировать права для обычных пользователей.
Зайдите в интерфейс под админом и в разделе администрирования во вкладке "Права" дайте нужные права пользователям. Либо же выполните запрос к endpoint:
```
POST /api/permissions/sync
{
    "role_id": 2, #basic users
    "permissions": []
}
```

Рекомендуемый набор прав: 
```
7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19
```

## ⚙️ Переменные окружения (.env)

| Переменная               | Описание                                        | Пример                  |
| :----------------------- |:------------------------------------------------|:------------------------|
| `APP_FRONT_URL`          | URL фронтенд-приложения                         | `http://localhost:8000` |
| `ADMIN_EMAIL`            | Email администратора                            | `admin@example.com`     |
| `ADMIN_PWD`              | Пароль администратора                           | `secret123`             |
| `BASIC_USER_EMAIL`       | Email тестового пользователя                    | `user@example.com`      |
| `BASIC_USER_PWD`         | Пароль тестового пользователя                   | `user123`               |
| `GOOGLE_CLIENT_ID`       | Client ID для Google OAuth                      | -                       |
| `GOOGLE_CLIENT_SECRET`   | Client Secret для Google OAuth                  | -                       |
| `FACEBOOK_CLIENT_ID`     | App ID для Facebook OAuth                       | -                       |
| `FACEBOOK_CLIENT_SECRET` | App Secret для Facebook OAuth                   | -                       |
