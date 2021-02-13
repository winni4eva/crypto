<?php

namespace App\Services\Blockchain\Clients\Bitgo;

use neto737\BitGoSDK\BitGoSDK;
use neto737\BitGoSDK\BitGoExpress;
use App\Services\Blockchain\Clients\ClientContract;

class BitgoClient implements ClientContract
{
    protected $bitGo;

    protected $bitGoExpress;

    protected $accessToken;

    protected $host;

    protected $port;

    protected $currency;

     /**
     * BitgoClient Initialization
     * 
     * @param string $accessToken  The developer account token
     * @param int $currency         The currency or coin identifier
     * @param string $appEnvironment      The current app environment
     */
    public function __construct(string $accessToken, string $currency = 'tbtc', bool $appEnvironment = false)
    {
        $this->accessToken = $accessToken;
        $this->host = config('crypto.host');
        $this->port = config('crypto.port');
        $this->currency = $currency;
        $this->bitgo = new BitGoSDK($accessToken, $currency, $appEnvironment);
        $this->bitgo->walletId = config('crypto.walletId');
        $this->bitGoExpress = new BitGoExpress(config('crypto.host'), config('crypto.port'), $currency);
        $this->bitGoExpress->walletId = config('crypto.walletId');
        $this->bitGoExpress->accessToken = $accessToken;
    }

    public function createWallet() 
    {   
        $label = $this->generateLabel();
        $passphrase = request('passphrase');

        $response = $this->bitGoExpress->generateWallet($label, $passphrase);

        if(collect($response)->has('error')) {
            return $this->handleErrorResponse($response);
        }
        
        return $response;
    }

    public function getWalletAddresses()
    {
        $response = $this->bitgo->getWalletAddresses(); 
        
        if(collect($response)->has('error')) {
            return $this->handleErrorResponse($response);
        }

        return $response;
    }

    public function createWalletAddress() 
    {
        $response = $this->bitgo->createWalletAddress();
        
        if(collect($response)->has('error')) {
            return $this->handleErrorResponse($response);
        }

        return $response;
    }

    public function updateWalletAddress(string $addressId, string $passphrase)
    {
        $response = $this->bitgo->updateWalletAddress($addressId, $passphrase);
        
        if(collect($response)->has('error')) {
            return $this->handleErrorResponse($response);
        }

        return $response;
    }

    public function listWallets()
    {
        $response = $this->bitgo->listWallets();
        
        if(collect($response)->has('error')) {
            return $this->handleErrorResponse($response);
        }

        return $response;
    }

    public function sendTransaction(string $recepientAddress, int $amount, string $passphrase, int $blocks)
    {
        $response = $this->bitGoExpress->verifyAddress($recepientAddress);
        
        if(!$response['isValid']) {
            return $response;
        }
        
        //$response = $this->bitgo->listWalletUnspents();
        //$response = $this->bitGoExpress->funoutWalletUnspents($passphrase);

        $params = [
            'address' => $recepientAddress,
            'amount' => request('amountCurrency') == 'usd'
                ? $this->convertToSatoshi($amount)
                : $amount,
            'walletPassphrase' => $passphrase,
            'minConfirms' => 1,
            'enforceMinConfirmsForChange' => true,
            'numBlocks' => $blocks,
        ];
        
        $response = $this->__execute('POST', $params);

        if(collect($response)->has('error')) {
            return $this->handleErrorResponse($response);
        }

        return $response;
    }

    public function getWalletTransactions() 
    {
        $response = $this->bitgo->getWalletTransactions();
        
        if(collect($response)->has('error')) {
            return $this->handleErrorResponse($response);
        }
        
        return $response;
    }

    public function getTotalBalances()
    {
        $response = $this->bitgo->getTotalBalances();
        
        if(collect($response)->has('error')) {
            return $this->handleErrorResponse($response);
        }
        
        return $response;
    }

    protected function handleErrorResponse($response)
    {
        logger($response);
        return $response;
    }

    protected function generateLabel()
    {
        $email = explode('@', auth()->user()->email); 
        $label = $email[0].'-'.now()->toDateTimeString('Y-m-d H:i:s');
        return $label;
    }

    public function convertToBtc(int $amount)
    {
        return BitGoSDK::toBTC($amount);
    }

    public function convertToSatoshi(int $amount)
    {
        return BitGoSDK::toSatoshi($amount);
    }

    private function __execute(string $requestType = 'POST', array $params = [],bool $array = true) 
    {
        $endpoint = 'http://'.$this->host.':'.$this->port.'/api/v2/'.$this->currency.'/wallet/'.config('crypto.walletId').'/sendcoins';
    
        $ch = curl_init($endpoint);
        
        if ($requestType === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array_filter($params)));
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (isset($this->accessToken)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->accessToken
            ]);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, $array);
    }
}