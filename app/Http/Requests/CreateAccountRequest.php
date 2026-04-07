<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users.email',
            'tipoConta' => 'required|integer',
            'cpf' => 'numeric|size:11|unique:fisicaccount.fisicaccount_cpf',
            'cnpj' => 'string|size:14|unique:juristicaccount.juristicaccount_cnpj',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ];
    }

    public function messages() {
        return [
            'name.required' => 'É necessário informar o nome do dono da conta!',
            'name.string' => 'O campo nome não possui um formato válido!',
            'name.max' => 'O campo nome deve conter no máximo 255 caracteres!',

            'email.required' => 'É necessário informar um email para a conta!',
            'email.string' => 'O email informado está inválido!',
            'email.email' => 'O campo email deve ser um email válido!',
            'email.max' => 'O campo email deve conter no máximo 255 caracteres!',
            'email.unique' => 'O email já está em uso!',

            'tipoConta.required' => 'É necessário informar o tipo da conta!',
            'tipoConta.integer' => 'É necessário informar o tipo da conta!',

            'cpf.numeric' => 'O campo cpf deve conter apenas números!',
            'cpf.size' => 'O campo cpf deve conter 11 caracteres!',
            'cpf.unique' => 'Já existe uma conta para esse cpf'


        ]
    }
}
