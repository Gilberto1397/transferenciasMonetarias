<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Services\TransferValueService;
use Illuminate\Http\JsonResponse;

class TransferController
{
    public function transferValue(TransferRequest $request, TransferValueService $service): JsonResponse
    {
        $response = $service->transferValue($request);
        return response()->json(
            ['message' => $response->getMessage(), 'error' => $response->hasError()],
            $response->getStatusCode()
        );
    }
}
