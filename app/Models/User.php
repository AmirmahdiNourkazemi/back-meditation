<?php

namespace App\Models;

use App\Traits\HasUuid;
use DateTimeInterface;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuid, Filterable;

    const GENDERS = [
        'male' => 1,
        'female' => 2,
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'full_name'
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAvatarAttribute()
    {
        return isset($this->attributes['avatar']) ? url($this->attributes['avatar']) : null;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function otpTokens()
    {
        return $this->hasMany(OtpTokens::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function packageNames()
    {
        return $this->belongsToMany(PackageName::class, 'user_package_name')->withPivot(['id', 'tries','fcm_token'])->withTimestamps();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'user_product')->withPivot(['expire_at', 'purchase_token', 'gateway'])->withTimestamps();
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }
    public function response()
    {
        return $this->hasOne(UserResponse::class);
    }
    
}