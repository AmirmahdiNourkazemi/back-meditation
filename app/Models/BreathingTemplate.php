<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreathingTemplate extends Model
{
    protected $fillable = ['user_id', 'name', 'inhale', 'exhale', 'repeat'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sessions()
    {
        return $this->hasMany(UserBreathingSession::class, 'template_id');
    }
}
