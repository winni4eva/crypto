<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    public function scopeGetEnvironmentCurrencies($query)
    {
        return app()->env == 'production' 
            ? $query->whereEnvironment('production')
            : $query->whereEnvironment('local');
    }
}
