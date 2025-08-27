<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mood extends Model
{
    protected $fillable = ['name', 'emoji', 'description'];

    public function userMoods()
    {
        return $this->hasMany(UserMood::class);
    }
}
