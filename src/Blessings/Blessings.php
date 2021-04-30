<?php


namespace Nikservik\Users\Blessings;

use Illuminate\Support\Facades\Config;
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
            json_decode($this->attributes[Config::get('users.blessings-attribute')])
        );
    }

    public function setBlessings(array $blessings): self
    {
        $this->attributes[Config::get('users.blessings-attribute')] = json_encode($blessings);

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

    protected function getBlesserContainers(): array
    {
        if (property_exists($this, 'blesserContainers')) {
            return static::$blesserContainers;
        }

        return [];
    }
}
