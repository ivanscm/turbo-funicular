## Тестовое задание

1. Есть две таблички: `topics` и `topics_messages`. Таблица `topics` содержит записи постов `(id|title|text)`. А табличка `topics_messages` содержит комментарии пользователей `(id|topics_id|users_id|comment|date_added)`. Задача: написать SQL запрос на выборку последних 3х комментариев для каждого поста в списке.
2. Написать на PHP сервис, который будет обеспечивать некоторый функционал по работе с текстом:
   - findAndRemove: для указанного набора слов в массиве  `['один', 'два', ...]` ищет вхождения по тесту и вырезает их.
   - findAndReplace: для указанного набора слов в массиве `['один' => 'foo', 'два' => 'bar', ...]` ищет вхождения в тесте по ключу и заменят их на значение в массиве.
3. Вывести на экран табличку с результатом со следующими колонками: Наименование топика / Последний комментарий из таблички topics_messages + ФИО автора комментария. (оформление на свое усмотрение)
4. В качестве демонстрации работы фильтров findAndRemove и findAndReplace предусмотреть некоторую форму, которая соответсвенно будет применять описанные во 2-ом пункте правила по работе с текстом для topics_messages.comment (оформление на свое усмотрение)

## Запуск
 - Переименуйте файл `example.dev` в `.env`. Если требуется установите необходимые параметры
 - Запустите окружение командой `docker compose up -d`
 - Выполните миграции БД командой `docker compose exec app php ./migrations/run.php structures basic-data dummy-data`