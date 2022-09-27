<?php


namespace Nikservik\Users\Blessings;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Config;
use Nikservik\Commons\Has;
use Nikservik\Users\Contracts\BlesserInterface;

/**
 * @property-read array<string> $blessings
 */
trait Blessings
{
    // Три варианта для хранения списка контейнеров с благословителями:
    // 1. Объявить статический атрибут blesserContainers со списком атрибутов-контейнеров
    //    protected static array $blesserContainers = ['cohorts', 'subscriptions'];
    // 2. Переопределить метод getBlesserContainers(), если нужен динамический список контейнеров
    //    protected function getBlesserContainers(): array
    // 3. Переопределить метод getBlessers(), если нужен динамический список благословителей
    //    public function getBlessers(): array

    /**
     * Проверяет, есть ли у пользователя заданное благословение
     * @param string $blessing
     * @return bool
     */
    public function blessedTo(string $blessing): bool
    {
        return in_array(
            $blessing,
            json_decode($this->attributes['blessings'])
        );
    }

    /**
     * Перезаписывает список благословений пользователю.
     * @param array $blessings
     * @return $this
     */
    public function setBlessings(array $blessings): self
    {
        $this->attributes['blessings'] = json_encode($blessings);

        return $this;
    }

    /**
     * Возвращает список благословителей пользователя.
     * Это могут быть, например, активные подписки пользователя и когорты, в которых он состоит.
     * @return array
     */
    public function getBlessers(): array
    {
        $blessers = [];

        foreach ($this->getBlesserContainers() as $container) {
            foreach ($this->$container as $blesser) {
                if ($blesser instanceof BlesserInterface) {
                    $blessers[] = $blesser;
                }
            }
        }

        return $blessers;
    }

    /**
     * Реализует метод whereBlessedTo для выборки из базы
     * @param Builder $query
     * @param string $blessing
     * @param $boolean
     * @return Builder
     */
    public function scopeWhereBlessedTo(Builder $query, string $blessing = '', $boolean = 'and'): Builder
    {
        $method = $boolean == 'and' ? 'where' : 'orWhere';

        return $query->$method(function ($query) use ($blessing) {
            $query->whereJsonContains('blessings', $blessing);
        });
    }

    /**
     * Реализует метод orWhereBlessedTo для выборки из базы
     * @param Builder $query
     * @param string $blessing
     * @return Builder
     */
    public function scopeOrWhereBlessedTo(Builder $query, string $blessing = ''): Builder
    {
        return $this->scopeWhereBlessedTo($query, $blessing, 'or');
    }

    /**
     * Возвращает список контейнеров с благословителями.
     * По умолчанию возвращает статический атрибут blesserContainers.
     * Можно переопределить, если нужно динамически менять список контейнеров благословителей.
     * @return array
     */
    protected function getBlesserContainers(): array
    {
        if (property_exists($this, 'blesserContainers')) {
            return static::$blesserContainers;
        }

        return [];
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if ($user->blessings === null) {
                if (Has::feature('users', 'load-default-blessings')) {
                    $user->blessings = json_encode(Config::get('users.default-blessings'));
                } else {
                    $user->blessings = '[]';
                }
            }
        });
    }
}
