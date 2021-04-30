<?php


namespace Nikservik\Users\Cohorts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Nikservik\Users\Blessings\UserBlessingsChanged;

trait Cohorts
{
    public function inCohort(string $cohort): bool
    {
        return in_array(
            $cohort,
            json_decode($this->attributes[Config::get('users.cohorts-attribute')])
        );
    }

    public function addToCohort(string $cohort): self
    {
        $cohorts = json_decode($this->attributes[Config::get('users.cohorts-attribute')]);

        if (! in_array($cohort, $cohorts)) {
            $cohorts[] = $cohort;

            $this->attributes[Config::get('users.cohorts-attribute')] = json_encode($cohorts);

            UserBlessingsChanged::dispatch($this);
        }

        return $this;
    }

    public function removeFromCohort(string $cohort): self
    {
        $cohorts = json_decode($this->attributes[Config::get('users.cohorts-attribute')]);

        if (in_array($cohort, $cohorts)) {
            unset($cohorts[array_search($cohort, $cohorts)]);

            $this->attributes[Config::get('users.cohorts-attribute')] = json_encode($cohorts);

            UserBlessingsChanged::dispatch($this);
        }

        return $this;
    }

    /**
     * Аксессор для атрибута cohorts
     * @return Collection
     */
    public function getCohortsAttribute()
    {
        return Cohort::
            whereIn('name', json_decode($this->attributes[Config::get('users.cohorts-attribute')]))
            ->get();
    }

    /**
     * Аксессор-обертка для исключительной подписки из пакета subscriptions
     * Чтобы использовать такую подписку как источник благословений,
     * достаточно добавить 'singleSubscription' в свойство $blesserContainers
     * @return array<Subscription>
     */
    public function getSingleSubscriptionAttribute(): array
    {
        return [$this->subscription];
    }
}