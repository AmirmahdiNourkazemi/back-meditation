<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
 protected $fillable = ['user_id', 'content', 'noteable_id', 'noteable_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function noteable()
    {
        return $this->morphTo();
    }
}
