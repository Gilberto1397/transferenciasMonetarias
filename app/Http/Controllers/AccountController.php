<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;

class AccountController
{
    public function createAccount(CreateAccountRequest $request)
    {
        dd($request->all());
    }
}
