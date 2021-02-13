<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    public function scopeLimitCountries($query)
    {
        $countriesToSelect = ['Ghana', 'United States', 'Togo', 'Senegal', 'Germany'];
        return app()->env == 'production' 
            ? $query
            : $query->whereIn('nicename', $countriesToSelect);
    }
}
