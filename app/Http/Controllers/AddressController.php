<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Address as AddressRequest;
use Facades\App\Services\Blockchain\BlockChainService;
use App\Services\Address\AddressService;

class AddressController extends Controller
{
    protected $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * Create a new address.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AddressRequest $request)
    {
        config(['crypto.currency' => $request->get('coin')]);
        config(['crypto.walletId' => $request->get('walletId')]);
        
        $address = BlockChainService::createWalletAddress();

        if(!$address) {
            return response()->json(['message' => 'Error creating address'], 422);
        }

        $this->addressService->saveAddress($address);

        BlockChainService::updateWalletAddress($address['id']);

        return response()->json(['success' => 'Address created successfully']);
    }
}
