<?php

namespace App\Http\Requests;

/**
 * @property string $name
 * @property string $email
 * @property int $tipoConta
 * @property string $cpf
 * @property string $cnpj
 * @property string $password
 * @property string $password_confirmation
 */
class CreateAccountRequest extends DefaultRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'tipoConta' => 'required|integer|exists:accounttypes,accounttypes_id',
            'cpf' => 'required_if:tipoConta,2|numeric|digits:11|unique:fisicaccount,fisicaccount_cpf',
            'cnpj' => 'required_if:tipoConta,1|numeric|digits:14|unique:juristicaccount,juristicaccount_cnpj',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'É necessário informar o nome do titular da conta!',
            'name.string' => 'O campo nome não possui um formato válido!',
            'name.max' => 'O campo nome deve conter no máximo 255 caracteres!',

            'email.required' => 'É necessário informar um email para a conta!',
            'email.string' => 'O email informado está inválido!',
            'email.email' => 'O campo email deve ser um email válido!',
            'email.max' => 'O campo email deve conter no máximo 255 caracteres!',
            'email.unique' => 'O email já está em uso!',

            'tipoConta.required' => 'É necessário informar o tipo da conta!',
            'tipoConta.integer' => 'É necessário informar o tipo da conta!',
            'tipoConta.exists' => 'O tipo de conta informado é inválido!',

            'cpf.numeric' => 'O campo cpf deve conter apenas números!',
            'cpf.digits' => 'O campo cpf deve conter 11 caracteres!',
            'cpf.unique' => 'Já existe uma conta para esse cpf!',
            'cpf.required_if' => 'O campo cpf é obrigatório para contas do tipo física!',

            'cnpj.numeric' => 'O campo cnpj está inválido!',
            'cnpj.digits' => 'O campo cnpj deve conter 14 caracteres!',
            'cnpj.unique' => 'Já existe uma conta para esse cnpj!',
            'cnpj.required_if' => 'O campo cnpj é obrigatório para contas do tipo jurídica!',

            'password.required' => 'É necessário informar uma senha para a conta!',
            'password.string' => 'A senha informada não é válida!',
            'password.min' => 'O campo senha deve conter no mínimo 8 caracteres!',
            'password.confirmed' => 'A confirmação de senha não corresponde à senha informada!',

            'password_confirmation.required' => 'É necessário confirmar a senha!',
            'password_confirmation.string' => 'A confirmação de senha informada não é válida!'
        ];
    }

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags(strval($this->name)),
            'email' => strip_tags(strval($this->email)),
            'cnpj' => strip_tags(strval($this->cnpj)),
            'password' => strip_tags(strval($this->password)),
            'password_confirmation' => strip_tags(strval($this->password_confirmation))
        ]);
    }
}
