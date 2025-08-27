<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMood extends Model
{
     protected $fillable = ['user_id', 'mood_id', 'date'];

     public function user()
     {
         return $this->belongsTo(User::class);
     }
     public function mood()
     {
         return $this->belongsTo(Mood::class);
     }
}
