<?php

namespace App\Services\Auth;

use App\Notifications\TwoFactorVerficationNotification;
use App\Models\User;

class TwoFactorAuthenticationService {

    public function __construct(){}

    public function sendToken() 
    {
        if(!auth()->check()) {
            return false;
        }

        $user = auth()->user();

        if($user->token_2fa_expiry < now()->toDateTimeString()){
            $this->setTokenExpiry($user);
            $this->setUserToken($user);
        }
        $user->notify(new TwoFactorVerficationNotification($user));
        return $this->getUserNotifiableChannel($user);
    }

    public function verifyToken(): bool
    {
        if(!auth()->check() || !request()->has('token')) {
            return false;
        }
        
        if(auth()->user()->token_2fa !== request()->get('token')) {
            return false;
        }

        return true;
    }

    protected function getUserNotifiableChannel(User $user)
    {
        if (request()->has('option_2fa') && request()->get('option_2fa')){
            return request()->get('option_2fa');
        }
        return $user->option_2fa;
    }

    protected function setTokenExpiry(User $user)
    {   
        $user->token_2fa_expiry = now()->add('10 minutes')->toDateTimeString();
    }

    protected function setUserToken(User $user)
    {
        $user->token_2fa = mt_rand(100000, 999999);
        $user->save();
    }
}