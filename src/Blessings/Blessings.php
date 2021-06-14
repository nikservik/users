<?php


namespace Nikservik\Users\Blessings;

use Illuminate\Database\Eloquent\Builder;
use Nikservik\Users\Contracts\BlesserInterface;

/**
 * @property string $blessings
 * @property string $cohorts
 * @property string $admin_role
 */
trait Blessings
{
    // Можно перечислить атрибуты, из которых будут собираться блессеры
    // protected static array $blesserContainers = ['cohorts'];
    // или перегрузить метод getBlessers()

    public function blessedTo(string $blessing): bool
    {
        return in_array(
            $blessing,
            json_decode($this->attributes['blessings'])
        );
    }

    public function setBlessings(array $blessings): self
    {
        $this->attributes['blessings'] = json_encode($blessings);

        return $this;
    }

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

    public function scopeWhereBlessedTo(Builder $query, string $blessing = '', $boolean = 'and'): Builder
    {
        $method = $boolean == 'and' ? 'where' : 'orWhere';

        return $query->$method(function ($query) use ($blessing) {
            $query->whereJsonContains('blessings', $blessing);
        });
    }

    public function scopeOrWhereBlessedTo(Builder $query, string $blessing = ''): Builder
    {
        return $this->scopeWhereBlessedTo($query, $blessing, 'or');
    }

    protected function getBlesserContainers(): array
    {
        if (property_exists($this, 'blesserContainers')) {
            return static::$blesserContainers;
        }

        return [];
    }
}
