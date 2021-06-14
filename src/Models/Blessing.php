<?php


namespace Nikservik\Users\Models;


use Illuminate\Database\Eloquent\Model;

class Blessing extends Model
{
    protected $fillable = ['slug', 'name', 'description'];

    public function folder()
    {
        return $this->belongsTo(BlessingsFolder::class, 'folder_id');
    }
}
