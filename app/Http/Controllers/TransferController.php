<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransferRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferController
{
    public function transferValue(TransferRequest $request)
    {
        DB::beginTransaction();
        $user1 = User::find($request->originId);
        $user2 = User::find($request->destinationId);

        if ($user1->balance < $request->value) {
            return response()
                ->json(
                    [
                        'message' => 'Saldo insuficiente para realizar a transferência!',
                        'error' => true
                    ],
                    400
                );
        }
        $user1->balance -= $request->value;
        $user2->balance += $request->value;

        $user1->save();
        $user2->save();
        DB::commit();

        return response()
            ->json(
                [
                    'message' => 'Transferência realizada com sucesso!',
                    'error' => false
                ],
                200
            );
    }
}
