<?php

namespace App\Services\Blockchain\Clients;


interface ClientContract
{
    public function listWallets();

    public function createWallet();

    public function getWalletAddresses();

    public function createWalletAddress();

    public function updateWalletAddress(string $addressId, string $passphrase);

    public function sendTransaction(string $recepientAddress, int $amount, string $passphrase, int $blocks);

    public function getWalletTransactions();
}