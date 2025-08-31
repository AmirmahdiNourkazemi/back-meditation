<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Worry extends Model
{
    protected $fillable = ['user_id', 'title', 'is_solved'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function notes()
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
