<?php

namespace Nikservik\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlessingsFolder extends Model
{
    protected $fillable = ['slug', 'name'];

    public $timestamps = false;

    public function blessings(): HasMany
    {
        return $this->hasMany(Blessing::class, 'folder_id');
    }
}
