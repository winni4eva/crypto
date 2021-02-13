<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\Funds as FundsRequest;
use Facades\App\Services\Blockchain\BlockChainService;
use App\Wallet;

class FundsController extends Controller
{
    /**
     * Return all wallet transactions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $wallet = Wallet::with('currency')->find(request()->get('wid'));//->with(['currency'])->first();

        config(['crypto.currency' => $wallet->currency->identifier]);
        config(['crypto.walletId' => $wallet->wallet_id]);

        $transactions = BlockChainService::getWalletTransactions();

        return response()->json(compact('transactions'));
    }
    /**
     * Create a new fund.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(FundsRequest $request)
    {
        config(['crypto.currency' => $request->get('coin')]);
        config(['crypto.walletId' => $request->get('walletId')]);
        
        $response = BlockChainService::sendTransaction(request('address'), request('amount'), request('passphrase'), request('block'));
        
        if(collect($response)->has('error') || !$response) {
            $message = $response['error'] ?? 'Failed sending coins';
            return response()->json(['message' => $message], 422);
        }

        return response()->json(['success' => 'Funds sent successfully']);
    }
}
