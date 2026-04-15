<?php

namespace App\Http\Requests;

use App\Rules\OnlyFisicAccounts;

/**
 * @property float $value
 * @property int $payer
 * @property int $payee
 */
class TransferRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, OnlyFisicAccounts|string>|string>
     */
    public function rules(): array
    {
        return [
            'value' => 'required|numeric',
            'payer' => ['required','integer', 'exists:users,id', new OnlyFisicAccounts()],
            'payee' => 'required|integer',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'value.required' => 'É necessário informar o valor da transferência!',
            'value.numeric' => 'O valor da transferência deve ser um número válido!',

            'payer.required' => 'É necessário informar a conta que fará à transferência!',
            'payer.integer' => 'A conta de origem está inválida!',
            'payer.exists' => 'A conta de origem informada não existe!',

            'payee.required' => 'É necessário informar a conta de destino da transferência!',
            'payee.integer' => 'A conta de destino está inválida!',
            'payee.exists' => 'A conta de destino informada não existe!'
        ];
    }
}
