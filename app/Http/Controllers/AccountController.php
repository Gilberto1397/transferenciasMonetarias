<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccountRequest;
use App\Services\CreateAccountService;
use Illuminate\Http\JsonResponse;

class AccountController
{
    public function createAccount(CreateAccountRequest $request, CreateAccountService $service): JsonResponse
    {
        $response = $service->createAccount($request);
        return response()->json(
            ['message' => $response->getMessage(), 'error' => $response->getError()],
            $response->getStatusCode()
        );
    }
}
