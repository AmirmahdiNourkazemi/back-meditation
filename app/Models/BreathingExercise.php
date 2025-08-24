<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreathingExercise extends Model
{
    public $timestamps = false; // we handle created_at manually
    protected $fillable = ['user_id', 'subject', 'duration', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
