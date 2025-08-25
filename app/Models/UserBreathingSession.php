<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBreathingSession extends Model
{
    protected $fillable = ['user_id', 'template_id'];

    public function template()
    {
        return $this->belongsTo(BreathingTemplate::class, 'template_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
