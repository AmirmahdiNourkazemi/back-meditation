<?php

namespace App\Models;

use App\Traits\HasUuid;
use Carbon\Carbon;
use DateTimeInterface;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasUuid, Filterable;

    protected $guarded = [];
    const TYPES = [
        'permanent' => 1,
        'yearly' => 2,
        '6months' => 3,
        'monthly' => 4,
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_product')->withPivot(['expire_at', 'purchase_token', 'gateway'])->withTimestamps();
    }

    public function packageName()
    {
        return $this->belongsTo(PackageName::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function buy($user, $purchaseToken = null, $gateway = null)
    {
        $expireDate = now();
        if ($purchaseUser = $this->users()->where('users.id', $user->id)->wherePivot('expire_at', '>=', now())->first()) {
            $expireDate = Carbon::parse($purchaseUser->pivot->expire_at) ?? now();
        }

        $expireAt = match ($this->type) {
            1 => null,
            2 => $expireDate->addYear()->format('Y-m-d H:i:s'),
            3 => $expireDate->addMonths(6)->format('Y-m-d H:i:s'),
            4 => $expireDate->addMonth()->format('Y-m-d H:i:s'),
        };

        if ($this->users()->where('users.id', $user->id)->exists()) {
            $this->users()->updateExistingPivot($user->id, [
                'expire_at' => $expireAt,
                'purchase_token' => $purchaseToken,
                'gateway' => $gateway,
            ]);
        } else {
            $this->users()->attach($user->id, [
                'expire_at' => $expireAt,
                'purchase_token' => $purchaseToken,
                'gateway' => $gateway,
            ]);
        }

        $user->transactions()->where('authority', $purchaseToken)->update(['status' => Transaction::STATUSES['consumed']]);
    }
}
