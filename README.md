# Авторизация пользователей и когорты 

Пакет для управления правами пользователей. 

## Установка

Добавить в `composer.json`
```json
{
    "require": {
        "nikservik/users": "^1.0"
    },
    "repositories" : [
        {
            "type": "vcs",
            "url" : "git@github.com:nikservik/users"
        }
    ]
}
```
После этого выполнить 
```bash
composer update
php artisan view:clear
```

Опубликовать файл конфигурации:
```bash
php artisan vendor:publish  --tag="users-config"
```
Перед запуском миграций нужно добавить в `config/users.php` 
емейл пользователя, который будет назначен владельцем ресурса. 
```php
    'owner' => 'user@gmail.com',
```
Ему будет назначен максимальный уровень доступа в админке.

Опубликовать миграцию и выполнить ее:
```bash
php artisan vendor:publish  --tag="users-migration"
php artisan migrate
```
После выполнения миграции можно удалить емейл владельца из конфигурации.

## Использование

### Авторизация действий пользователя
Нужно добавить трейт `Blessings` в модель User.
```php
class User extends Auth
{
    use Blessings;
}
```
blessing = «благословение» – это аналог прав или разрешений для пользователя.
В стандартной модели авторизации используется метод `can`.
В этом пакете используется метод `blessedTo`.

```php
    if (Auth::user()->blessedTo('use-predictions')) {
        // показать предсказания
    }
```
#### Авторизация в шаблонах blade
```blade
    @blessed('use-predictions')
        Предсказание для ...
    @else
        У вас не прав для ...
    @endbless
```

### Назначение прав пользователю
Для назначения прав можно использовать метод `setBlessings`.
```php
    Auth::user()->setBlessings(['use-predictions', 'see-dashas']);
```
При вызове этого метода благословения пользователя полностью заменяются на новые и сохраняются в базу.

### Динамическое назначение благословений
Удобнее пользоваться автоматическим обновлением благословений.
Для этого нужно использовать классы-источники благословений.

Класс-благословитель должен реализовывать интерфейс `BlesserInterface`.
```php
class Subscription implements BlesserInterface
{
    protected bool $active = true;
    
    public function getBlessings(): array
    {
        if ($this->active) {
            return ['use-predictions', 'see-dashas'];
        } else {
            return [];
        }
    }
}
```
В модели пользователя должен быть атрибут, который возвращает список классов-благословителей.
И этот атрибут должен быть добавлен статическое свойство `blesserContainers`.
```php
// Пример регистрации подписок пользователя как источников благословений
class User extends Auth
{
    protected static array $blesserContainers = ['subscriptions'];
    
    // подписки пользователя
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
```
Если у пользователя может быть только одна активная подписка, то можно использовать
специальный трейт `SingleSubscriptionBlessings`. 
Он добавляет аксессор для атрибута `singleSubscription`.
Этот атрибут нужно добавить в список благословителей `$blesserContainers`.
```php
class User extends Auth
{
    use SingleSubscriptionBlessings;
    
    protected static array $blesserContainers = ['singleSubscription'];
    
    // подписка пользователя
    public function subscription()
    {
        return $this->hasOne(Subscription::class);
    }
}
```
В `blesserContainers` может быть несколько атрибутов. При обновлении благословений, все они будут обработаны.
Если нужно динамически создавать список источников благословений, 
то можно переопределить метод `getBlesserContainers`.

При изменении состояния класс-благословитель должен создавать событие `UserBlessingsChanged`.
```php
class Subscription implements BlesserInterface
{
    protected bool $active = true;
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function unsubscribe()
    {
        $this->active = false;
        
        UserBlessingsChanged::dispatch($this->user);
    }
}
```
### Структура благословений
В админке благословения разбиты на группы. 
Эта структура используется для удобства, например, при создании тарифов.
На фронте названия благословений не нужны, как и разбивка на группы.

Структура благословений хранится в проекте админки в `config/blessings.php`.
```php
return [
    'astro' => [
        'astro-arudhas',
        'astro-birth-panchanga',
    ],
    'usability' => [
        'usability-birthday-remind',
        'usability-settings-useful',
        'premium',
    ],
];
```

Названия папок и самих благословений хранятся в проекте админки в `resources/lang/ru/blessings.php`.
```php
return [
    'astro-arudhas' => 'Арудхи',
    'astro-birth-panchanga' => 'Панчанга рождения',

    'usability-birthday-remind' => 'Напоминания о днях рожения',
    'usability-settings-useful' => 'Настройка карт "Под рукой"',
    'premium' => 'Привилегии Премиум',

    'folders' => [
        'astro' => 'Астро-расчеты',
        'usability' => 'Удобства',
    ],
];
```

### Middleware для проверки прав

#### Проверка благословений
Для проверки благословений в роуте используется `UserBlessedTo` или сокращение `blessed`.
```php
    \Illuminate\Support\Facades\Route::get('/some/path', SomeAction::class)
        ->middleware("blessed:required-blessing");
```
Если у пользователя нет `required-blessing`, то роутер вернет редирект на 403.

#### Проверка прав администратора
Чтобы проверить уровень прав администратора используется `HasAdminRole` или сокращение `admin`.
```php
    \Illuminate\Support\Facades\Route::get('/some/admin/path', SomeAction::class)
        ->middleware("admin:editor");
```
Можно указывать минимальную роль словом: `editor`, `admin`, `superadmin`.
Или числом: 2, 3, 4.
1 - это уровень обычного пользователя.

### Когорты
Когорты – это инструмент для тестирования гипотез.
Они тоже реализуют интерфейс `BlesserInterface`.

Для использования когорт нужно добавить трейт `Cohorts` в модель пользователя.
Для добавления пользователя в когорту используется метод `addToCohort`.
Для исключения пользователя из когорты – `removeFromCohort`.

При вызове этих методов у пользователя создается событие `UserBlessingsChanged` 
и благословения пользователя обновляются автоматически.

## Тестирование

```bash
phpunit
```

## История изменений
### 1.06
- обновленные когорты
    - прописываются в отдельном конфиге (больше не хранятся в базе)
    - хранят скоуп в классе
- структура благословений вынесена в конфиг админки

### 1.05
- добавлена фича благословений по умолчанию
    - включается/выключается как и все фичи
    - можно прописать в конфиге список благословений по умолчанию, они выдаются всем пользователям до назначения первого благословителя
    - исправлен глюк отсутствия благословений

### 1.04
- добавлены middleware для проверки благословений и прав администратора

### 1.03
- добавлены модели благословений и папки благословений

### 1.02
- добавлены методы whereBlessedTo и orWhereBlessedTo

### 1.01
- добавлен трейт AdminRoles

### 1.0
- Базовый функционал

## TODO

