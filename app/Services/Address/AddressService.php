<?php

namespace App\Services\Address;

use App\Models\Address;
use App\Models\Wallet;

class AddressService
{
    protected $address;

    protected $wallet;

    public function __construct(Address $address, Wallet $wallet)
    {
        $this->address = $address;
        $this->wallet = $wallet;
    }

    public function saveAddress(array $address): void 
    {   
        $userId = auth()->user()->id;
        $wallet = $this->wallet->whereUserId($userId)
                    ->where('wallet_id', $address['wallet'])
                    ->first();
        
        $this->address->create([
            'wallet_id' => $wallet->id,
            'address_id' => $address['id'],
            'addresss' => $address['address'],
            'dump' => $address
        ]);
    }

    public function saveAddresses(array $addresses, string $walletId): void 
    {   
        $userId = auth()->user()->id;
        $wallet = $this->wallet->whereUserId($userId)
                    ->where('wallet_id', $walletId)
                    ->first();

        $addresses = $this->formatAddresses($addresses, $wallet);

        $wallet->addresses()->createMany($addresses);
    }

    protected function formatAddresses(array $addresses, Wallet $wallet)
    {
        return collect($addresses)->map(function($address) use($wallet){
            return [
                'wallet_id' => $wallet->id,
                'address_id' => $address['id'],
                'addresss' => $address['address'],
                'dump' => $address
            ];
        })->all();
    }

}