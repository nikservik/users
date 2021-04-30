<?php
// config for Nikservik/UserSettings
return [
    // Какие возможности включены
    // Чтобы отключить возможность, достаточно ее закомментировать
    'features' => [
        'create-blessings-attribute',
        'create-admin-role-attribute',
        'create-cohorts-attribute',
        'register-policies',
        'register-blade-directives',
    ],


    // В каком атрибуте модели User хранятся права доступа
    'blessings-attribute' =>  'blessings',
    // В каком атрибуте модели User хранится уровень доступа в админке
    'admin-role-attribute' => 'admin_role',
    // В каком атрибуте модели User хранятся когорты пользователя
    'cohorts-attribute' => 'cohorts',

    // Емейл пользователя, которому будет присвоен максимальный доступ в админке
    // Используется только при выполнении миграции
    // После выполнения миграции эту настройку можно удалить
    'owner' => 'ser.nikiforov@gmail.com',
];
