<?php


namespace Nikservik\Users\Cohorts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Nikservik\Users\Blessings\UserBlessingsChanged;

trait Cohorts
{
    /**
     * Проверяет, находится ли пользователь в заданной когорте
     * @param string $cohort
     * @return bool
     */
    public function inCohort(string $cohort): bool
    {
        return in_array(
            $cohort,
            json_decode($this->attributes['cohorts'])
        );
    }

    /**
     * Добавляет пользователя в заданную когорту.
     * Инициирует событие UserBlessingsChanged.
     * @param string $cohort
     * @return $this
     */
    public function addToCohort(string $cohort): self
    {
        $cohorts = json_decode($this->attributes['cohorts']);

        if (! in_array($cohort, $cohorts)) {
            $cohorts[] = $cohort;

            $this->attributes['cohorts'] = json_encode($cohorts);

            UserBlessingsChanged::dispatch($this);
        }

        return $this;
    }

    /**
     * Удаляет пользователя из заданной когорты.
     * Инициирует событие UserBlessingsChanged.
     * @param string $cohort
     * @return $this
     */
    public function removeFromCohort(string $cohort): self
    {
        $cohorts = json_decode($this->attributes['cohorts']);

        if (in_array($cohort, $cohorts)) {
            unset($cohorts[array_search($cohort, $cohorts)]);

            $this->attributes['cohorts'] = json_encode($cohorts);

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
        return $this->cohorts()->get();
    }

    /**
     * Возвращает Builder со списком когорт пользователя
     * @return mixed
     */
    public function cohorts()
    {
        return Cohort::
            whereIn('name', json_decode($this->attributes['cohorts']));
    }
}
