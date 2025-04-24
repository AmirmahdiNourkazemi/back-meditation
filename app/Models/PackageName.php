<?php

namespace App\Models;

use App\Traits\HasUuid;
use DateTimeInterface;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class PackageName extends Model
{
    use HasUuid, Filterable;
    protected $guarded = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_package_name')->withPivot(['id', 'tries'])->withTimestamps();
    }

    public function getImageAttribute()
    {
        return url($this->attributes['image']);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function cafeConfig()
    {
        return $this->belongsTo(CafeConfig::class);
    }
}
