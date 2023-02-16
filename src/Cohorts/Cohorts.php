<?php


namespace Nikservik\Users\Cohorts;

use Illuminate\Support\Facades\Config;
use Nikservik\Users\Blessings\UserBlessingsChanged;

trait Cohorts
{
    protected array $initiatedCohorts;

    /**
     * Проверяет, находится ли пользователь в заданной когорте
     * @param string $cohort
     * @return bool
     */
    public function inCohort(string $cohort): bool
    {
        return isset($this->attributes['cohorts'])
            && $this->attributes['cohorts'] !== null
            && in_array(
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
        $cohorts = json_decode($this->attributes['cohorts'] ?? '[]');

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
     * @return array<Cohort>
     */
    public function getCohortsAttribute()
    {
        return $this->cohorts();
    }

    /**
     * Возвращает инициализированные когорты пользователя.
     * Здесь используется только базовый класс Cohort,
     * потому что от него нужен только список благословений.
     * @return array<Cohort>
     * @throw BadCohortNameException
     * @throw NonExistentCohortClassException
     */
    public function cohorts(): array
    {
        if (isset($this->initiatedCohorts)) {
            return $this->initiatedCohorts;
        }

        if ($this->attributes['cohorts'] === null) {
            return [];
        }

        $this->initiatedCohorts = [];

        foreach (json_decode($this->attributes['cohorts'], true) as $cohort) {
            if ($cohortData = Config::get('cohorts.'.$cohort)) {
                $this->initiatedCohorts[] = new Cohort($cohort, $cohortData['blessings']);
            }
        }

        return $this->initiatedCohorts;
    }
}
