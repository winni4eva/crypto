<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Funds extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(!auth()->check()) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'amount' => 'required|numeric',
            'address' => 'required|string',
            'walletId' => 'required|string',
            'coin' => 'required|string',
            'block' => 'required|string',
            'amountCurrency' => 'required|string',
        ];
    }
}
