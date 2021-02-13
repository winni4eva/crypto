<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Currency;
use App\Address;
use App\User;

class Wallet extends Model
{
    protected $fillable = ['wallet_id', 'user_id', 'currency_id', 'label', 'keys', 'key_signatures', 'dump', 'passphrase'];

    protected $hidden = ['keys','key_signatures','dump'];

    protected $casts = [
        'keys' => 'array', 
        'key_signatures' => 'array', 
        'dump' => 'array'
    ];

    public function scopeUserWallets($query)
    {
        $userId = auth()->user()->id;

        return $query->whereUserId($userId);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
