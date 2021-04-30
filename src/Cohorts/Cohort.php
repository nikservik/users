<?php


namespace Nikservik\Users\Cohorts;

use Illuminate\Database\Eloquent\Model;
use Nikservik\Users\Contracts\BlesserInterface;

/**
 * @property-read int $id
 * @property string $name
 * @property array $blessings
 */
class Cohort extends Model implements BlesserInterface
{
    protected $fillable = ['name', 'blessings'];

    protected $casts = [
        'blessings' => 'array',
    ];

    public $timestamps = false;

    public function getBlessings(): array
    {
        return $this->blessings;
    }
}
