<?php

namespace App\Http\Controllers;

use App\Helpers\CreateLog;
use App\Http\Requests\CreateAccountRequest;
use App\Services\CreateAccountService;
use Illuminate\Http\JsonResponse;
use PHPUnit\Util\Exception;

class AccountController
{
    public function createAccount(CreateAccountRequest $request, CreateAccountService $service): JsonResponse
    {
        try {
            $response = $service->createAccount($request);
            return response()->json(
                ['message' => $response->getMessage(), 'error' => $response->getError()],
                $response->getStatusCode()
            );
        } catch (\DomainException $e) {
            CreateLog::logError($exception->getMessage(), $exception->getFile(), $exception->getLine(), $request->all());
            return response()->json(
                ['message' => $e->getMessage(), 'error' => true],
                400
            );
        }
    }
}
