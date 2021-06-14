<?php


namespace Nikservik\Users\Models;


use Illuminate\Database\Eloquent\Model;

class BlessingsFolder extends Model
{
    protected $fillable = ['slug', 'name', 'description'];

    public function blessings()
    {
        return $this->hasMany(Blessing::class);
    }
}
