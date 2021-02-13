<?php

namespace App\Services\Blockchain;

use App\Services\Blockchain\Clients\ClientContract;

class BlockChainService
{
    protected $client;

    public function __construct(ClientContract $client)
    {
        $this->client = $client;
    }

    public function createWallet() 
    {   
        return $this->client->createWallet();
    }

    public function getWalletAddresses()
    {
        return $this->client->getWalletAddresses();
    }

    public function createWalletAddress() 
    {
        return $this->client->createWalletAddress();
    }

    public function updateWalletAddress(string $addressId, string $passphrase)
    {
        return $this->client->updateWalletAddress($addressId, $passphrase);
    }

    public function listWallets()
    {
        return $this->client->listWallets();
    }

    public function getTotalBalances()
    {
        return $this->client->getTotalBalances();
    }

    public function sendTransaction(string $recepientAddress, $amount, $passphrase, int $blocks) 
    {
        return $this->client->sendTransaction($recepientAddress, $amount, $passphrase, $blocks);
    }

    public function getWalletTransactions()
    {
        return $this->client->getWalletTransactions();
    }

    public function convertToBtc($amount)
    {
        return $this->client->convertToBtc($amount);
    }

}