<?php

namespace App\Models;

use App\Traits\HasUuid;
use DateTimeInterface;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{    
    use HasUuid, Filterable;

    protected $guarded = [];
    const STATUSES = [
        'pending' => 1,
        'success' => 2,
        'consumed' => 3,
        'failed' => 3,
    ];
    const GATEWAYS = [
        'asanpardakht' => 1,
        'zarinpal' => 2,
        'digipay' => 3,
        'cafe' => 4,
        'myket' => 5,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
