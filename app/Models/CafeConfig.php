<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CafeConfig extends Model
{
    use HasFactory, HasUuid;

    protected $guarded = [];

    public function packageNames()
    {
        return $this->hasMany(PackageName::class);
    }
}
