<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = ['wallet_id', 'address_id', 'addresss', 'dump'];

    protected $hidden = ['dump'];

    protected $casts = ['dump' => 'array'];
}
