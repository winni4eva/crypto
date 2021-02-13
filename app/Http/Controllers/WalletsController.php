<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Wallet as WalletRequest;
use App\Services\Wallet\WalletService;
use App\Services\Address\AddressService;
use App\Models\Wallet;

class WalletsController extends Controller
{
    protected $walletService;

    protected $addressService;

    public function __construct(WalletService $walletService, AddressService $addressService)
    {
        $this->walletService = $walletService;
        $this->addressService = $addressService;
    }

    /**
     * Return all wallets.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wallets = $this->walletService->getWallets();
        
        $walletBalances = $this->walletService->getWalletBalances($wallets);

        $wallets = collect($wallets)->map(function($wallet) use ($walletBalances) {
            $walletBalance = collect($walletBalances)->where('wallet_id', $wallet['wallet_id'])->first();
            if($walletBalance) {
                return collect($wallet)->prepend($walletBalance['balance'], 'balance')->all();
            }
        });

        return response()->json(compact('wallets'));
    }

    /**
     * Create a new wallet.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(WalletRequest $request)
    {
        config(['crypto.currency' => $request->get('coin')]);
        $passphrase = $request->get('passphrase');

        $wallet = $this->walletService->createWallet();

        if(!$wallet) {
            return response()->json(['message' => 'Error creating wallet'], 422);
        }

        $this->walletService->saveWallet($wallet, $passphrase);
        config(['crypto.walletId' => $wallet['id']]);
        $addresses = $this->walletService->getWalletAddresses();

        if(!$addresses) {
            return response()->json(['message' => 'Error fetching wallet address'], 422);
        }

        $this->addressService->saveAddresses($addresses['addresses'], $wallet['id']);
        
        $addressId = collect($addresses['addresses'])->first()['id'];
        $this->walletService->updateWalletAddress($addressId, $passphrase);

        return response()->json(['success' => 'Wallet created successfully']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $currency = request('coin'); 

        return response()->json(['message' => 'Wallet deleted successfully']);
    }

    public function getTransactions(Wallet $wallet)
    {
        config(['crypto.currency' => request('coin')]);
        config(['crypto.walletId' => $wallet->wallet_id]);

        $response = $this->walletService->getWalletTransactions();

        if(collect($response)->has('error') || !$response) {
            $message = $response['error'] ?? 'Failed fetching transactions';
            return response()->json(['message' => $message], 422);
        }

        return response()->json(['transactions'=> $response]);
    }
}
