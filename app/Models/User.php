<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
      'first_name', 
      'last_name', 
      'email', 
      'password', 
      'phone_number', 
      'white_listed_ips',
      'option_2fa',
      'token_2fa',
      'token_2fa_expiry',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'white_listed_ips',
        'option_2fa',
        'token_2fa',
        'token_2fa_expiry',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'white_listed_ips' => 'json',
    ];

    public function getJWTIdentifier() {
      return $this->getKey();
    }

    public function getJWTCustomClaims() {
        return [];
    }
}
