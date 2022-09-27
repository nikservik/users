<?php
// config for Nikservik/Users
return [
    // Какие возможности включены
    // Чтобы отключить возможность, достаточно ее закомментировать
    'features' => [
        'create-blessings-attribute',
        'create-admin-role-attribute',
        'create-cohorts-attribute',
        'register-event-listener',
        'register-blade-directives',
        'register-middleware',
        'load-default-blessings',
    ],

    // Емейл пользователя, которому будет присвоен максимальный доступ в админке
    // Используется только при выполнении миграции
    // После выполнения миграции эту настройку можно удалить
    'owner' => 'ser.nikiforov@gmail.com',

    // Список благословений, которые получают пользователи по умолчанию,
    // до включения подписки или подключения к когорте
    'default-blessings' => ['default'],
];
