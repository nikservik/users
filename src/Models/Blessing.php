<?php

namespace Nikservik\Users\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Blessing extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'position',];

    public $timestamps = false;

    public function folder(): BelongsTo
    {
        return $this->belongsTo(BlessingsFolder::class, 'folder_id');
    }
}
