# Тестовое задание на позицию backend-разработчика (PHP)

## Запуск приложения

1. Копируем к себе репозиторий
2. Создаем ```.env``` и копируем туда ```env.example```
3. ``docker-compose up --build``
4. ``docker-compose exec app php artisan key:generate``
5. ``docker-compose exec app php artisan migrate``

Приложение доступно по `localhost:8080`

Документация к api находится в ```api.json```

## Запуск тестов

``docker-compose exec app php artisan test``

