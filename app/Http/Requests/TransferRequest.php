<?php

namespace App\Http\Requests;

class TransferRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'value' => 'required|numeric',
            'originId' => 'required|integer|exists:fisicaccounts,fisicaccount_id',
            'destinationId' => 'required|integer',
            'accountTypeDestination' => 'required|integer|exists:accounttypes,accounttypes_id'
        ];
    }

    public function messages()
    {
        return [
            'value.required' => 'É necessário informar o valor da transferência!',
            'value.numeric' => 'O valor da transferência deve ser um número válido!',

            'originId.required' => 'É necessário informar a conta que fará à transferência!',
            'originId.integer' => 'A conta de origem está inválida!',
            'originId.exists' => 'A conta de origem informada não existe!',

            'destinationId.required' => 'É necessário informar a conta de destino da transferência!',
            'destinationId.integer' => 'A conta de destino está inválida!',
            'destinationId.exists' => 'A conta de destino informada não existe!',

            'accountTypeDestination.required' => 'É necessário informar o tipo da conta!',
            'accountTypeDestination.integer' => 'O tipo da conta está inválido!',
            'accountTypeDestination.exists' => 'O tipo da conta informado não existe!'
        ];
    }
}
