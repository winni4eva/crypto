<?php

namespace App\Services\Wallet;

use App\Models\Wallet;
use App\Models\Currency;
use App\Services\Blockchain\BlockChainService;
use Facades\App\Services\CurrencyConverter\CurrencyConverterService;

class WalletService
{
    protected $wallet;

    protected $currency;

    public function __construct(Wallet $wallet, Currency $currency)
    {
        $this->wallet = $wallet;
        $this->currency = $currency;
    }

    public function getWallets(): array
    {
        return $this->wallet->userWallets()
                    ->with(['currency', 'addresses','user'])
                    ->get()
                    ->toArray();
    }

    public function saveWallet(array $wallet, string $passphrase): void 
    {   
        $currency = $this->currency->whereIdentifier($wallet['coin'])->first();

        $wallet = $this->wallet->create([
            'wallet_id' => $wallet['id'],
            'user_id' => auth()->user()->id,
            'currency_id' => $currency->id,
            'label' => $wallet['label'],
            'keys' => $wallet['keys'],
            'passphrase' => $passphrase,
            'key_signatures' => $wallet['keySignatures'],
            'dump' => $wallet
        ]);
    }

    public function createWallet()
    {
        return resolve(BlockChainService::class)->createWallet();
    }

    public function getWalletAddresses()
    {
        return resolve(BlockChainService::class)->getWalletAddresses();
    }

    public function updateWalletAddress(string $addressId, string $passphrase)
    {
        resolve(BlockChainService::class)->updateWalletAddress($addressId, $passphrase);
    }

    public function getWalletTransactions()
    {
        $response = resolve(BlockChainService::class)->getWalletTransactions();
        
        if (collect($response)->has('transactions')) {
            $response = collect($response['transactions'])->map(function($transaction){
                return [
                    'id' => $transaction['id'],
                    'date' => $transaction['date'],
                    'confirmations' => $transaction['confirmations'],
                    'address' => $transaction['inputs'][0]['address'] ?? '',
                    'value' => CurrencyConverterService::convert(
                        $this->getBtcValue($transaction['inputs'][0]['value'] ?? 0), config('crypto.currency'), 'usd'),
                ];
            })->values();
        }
        
        return $response;
    }

    public function getWalletBalances(array $wallets)
    {
        $walletBalances = collect($wallets)->map(function($wallet){
            return [
                'wallet_id' => $wallet['wallet_id'],
                'currency' => $wallet['currency']['identifier']
            ];
        })->groupBy('currency')
        ->map(function($data, $currency){
            config(['crypto.currency' => $currency]);
            return resolve(BlockChainService::class)->listWallets();
        })->map(function($fetchedWallet){
            return $fetchedWallet['wallets'];
        })->map(function($filteredWallet) {
            return collect($filteredWallet)->map(function($wallet){
                return [
                    'wallet_id' => $wallet['id'],
                    'balance' => [
                        'balance' => CurrencyConverterService::convert(
                            $this->getBtcValue($wallet['balance']), $wallet['coin'], 'usd'),
                        'confirmedBalance' => CurrencyConverterService::convert(
                            $this->getBtcValue($wallet['confirmedBalance']), $wallet['coin'], 'usd'),
                        'spendableBalance' => CurrencyConverterService::convert(
                            $this->getBtcValue($wallet['spendableBalance']), $wallet['coin'], 'usd'),
                        'balanceBtc' => $this->getBtcValue($wallet['balance']),
                        'confirmedBalanceBtc' => $this->getBtcValue($wallet['confirmedBalance']),
                        'spendableBalanceBtc' => $this->getBtcValue($wallet['spendableBalance']),
                    ]
                ];
            })->all();
        })->flatten(1);

        return $walletBalances;
    }

    public function getBtcValue($amount)
    {
        return resolve(BlockChainService::class)->convertToBtc($amount);
    }

}