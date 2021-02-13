<?php

namespace App\Services\CurrencyConverter;

class CurrencyConverterService
{
    const TBTC_USD = 8679.95;
    
    const USD_TBTC = 0.00012;

    const TBCH_USD = 246.03;
    
    const USD_TBCH = 0.0041;

    const TLTC_USD = 58.65;

    const USD_TLTC = 0.017;

    protected $currencies = ['USD', 'TBTC', 'TBCH', 'TLTC'];

    /** 
     * 
     * Should make use of an external service like coinlayer
     * 
    */
    public function convert($amount, $from, $to)
    {   
        $from = strtoupper($from);
        $to = strtoupper($to);
        
        if (!in_array($from, $this->currencies) || !in_array($to, $this->currencies)) {
            throw new \Exception("Currencies passed are not supported. Convert {$from} to {$to}", 422);
        }
        
        $key = $from.'_'.$to;
        $rate = constant("self::$key");
        
        return round(($amount * $rate), 2);
    }
}