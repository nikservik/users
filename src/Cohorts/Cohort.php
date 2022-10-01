<?php


namespace Nikservik\Users\Cohorts;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Nikservik\Users\Cohorts\Exceptions\BadCohortNameException;
use Nikservik\Users\Cohorts\Exceptions\NonExistentCohortClassException;
use Nikservik\Users\Contracts\BlesserInterface;

class Cohort implements BlesserInterface
{
    /**
     * Уникальное название когорты
     * К нему привязана запись в конфиге cohorts
     * @var string $name
     */
    protected string $name;

    /**
     * Список благословений когорты
     * @var array $blessings
     */
    protected array $blessings;

    public function __construct(string $name, array $blessings)
    {
        $this->name = $name;
        $this->blessings = $blessings;
    }

    /**
     * Возвращает уникальное название когорты
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Реализует интерфейс BlesserInterface
     * @return array
     */
    public function getBlessings(): array
    {
        return $this->blessings;
    }

    /**
     * Фабрика, настройки когорты берет в конфиге cohorts.php
     * @param string $name
     * @return static|null
     * @throws BadCohortNameException
     * @throws NonExistentCohortClassException
     */
    public static function make(string $name): self
    {
        $cohortData = Config::get('cohorts.'.$name);
        if (! $cohortData) {
            throw new BadCohortNameException('Config file does not contain cohort with name "' . $name . '"');
        }

        $cohortClass = $cohortData['class'];
        if (! class_exists($cohortClass)) {
            throw new NonExistentCohortClassException('Cohort class ' . $cohortData['class'] . ' specified for cohort "' . $name . '" does not exist');
        }

        return new $cohortClass($name, $cohortData['blessings']);
    }

    /**
     * Возвращает количество пользователей, состоящих в когорте
     * @return int
     */
    public function getUsersCount(): int
    {
        return User::whereJsonContains('cohorts', $this->name)->count();
    }

    /**
     * Возвращает количество пользователей, подходящих под условия когорты
     * @return int
     */
    public function getQualifyingUsersCount(): int
    {
        return User::where(function ($query) {$this->scope($query);})
            ->count();
    }

    /**
     * Добавляет в когорту пользователей, подходящих под условия когорты
     * @return void
     */
    public function addQualifyingUsers(): void
    {
        User::where(function ($query) {$this->scope($query);})
            ->update(['cohorts' => DB::raw("JSON_ARRAY_APPEND(cohorts, '$', '".$this->name."')")]);

        $this->updateBlessingsForUsers();
    }

    /**
     * Добавляет условия выборки пользователей в когорту
     * @param Builder $query
     * @return void
     */
    protected function scope($query): void
    {
        // $query->where(...);
    }

    protected function updateBlessingsForUsers()
    {
        foreach ($this->blessings as $blessing) {
            User::whereJsonContains('cohorts', $this->name)
                ->whereJsonDoesntContain('blessings', $blessing)
                ->update(['blessings' => DB::raw("JSON_ARRAY_APPEND(blessings, '$', '".$blessing."')")]);
        }
    }
}
