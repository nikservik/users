# Когорты

## Основная идея

Когорты – это способ группировки пользователей. 

Каждая когорта содержит в себе скоуп для выборки пользователей. 
Сейчас это основное использование когорт – группировать существующих пользователей.
Позже можно будет добавлять пользователей в когорту не по скоупу, а по каким-то событиям.

## Что они умеют
- Когорты хранят благословения. То есть через них можно включать что-то выбранным пользователям.
- Можно добавить в когорту всех пользователей, попадающих в скоуп. Благословения у них обновятся при добавлении.
- Можно посмотреть, сколько пользователей в когорте есть и сколько попадает в скоуп.

## Как создать когорту
Когорта создается в самом проекте, а не в модуле. 
Это, в первую очередь, относится к конфигу.

В файле `config/cohorts.php` нужно прописать название, класс и благословения.
Если это фронт, то можно не прописывать класс. Класс нужен только для админки.
```php
<?php

return [
    // Когорта старого тарифа Премиум
    'premium' => [
        'class' => \App\Cohorts\OldTariffs\PremiumCohort::class,
        'blessings' => [
            'premium',
        ],
    ],
];
```

В админке нужно создать класс когорты и определить в нем скоуп. 
Его можно делать в модуле админки когорт, скоуп используется только там. 
```php
<?php

namespace App\Cohorts\OldTariffs;

use Nikservik\Users\Cohorts\Cohort;

class PremiumCohort extends Cohort
{
    protected function scope($query): void
    {
        $query->where('settings->tariff->type', '=', 'premium');
    }
}
```

## Модель пользователя
У пользователя нужно добавить трейт `Cohorts`.
И прописать когорты как источники благословений

```php
<?php

namespace App\Models;

class User extends Auth
{
    use Cohorts;

    protected static array $blesserContainers = ['cohorts'];
```

### Управление когортами пользователя
Для добавления пользователя в когорту используйте метод `addToCohort`.
```php
$user->addToCohort('premium');
```
После добавления в когорту будет сформировано событие `UserBlessingsChanged`
и благословения пользователя будут пересчитаны. 
Для этого должна быть включена фича `register-event-listener` в файле `config/users.php` (по умолчанию она включена).

Для удаления пользователя из когорты используйте метод `removeFromCohort`.
```php
$user->removeFromCohort('premium');
```
После удаления из когорты благословения также пересчитываются.

Для проверки, состоит ли пользователь в когорте, используйте метод `inCohort`.
```php
if ($user->inCohort('premium')) {
    // ...
}
```