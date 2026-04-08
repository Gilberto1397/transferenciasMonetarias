<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;

class AccountController
{
    public function createAccount(CreateAccountRequest $request): void
    {
        dd($request->all());
    }
}
