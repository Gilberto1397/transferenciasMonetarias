<?php

namespace App\Services;

use App\Clients\AuthorizationClient;
use App\Contracts\UserRepository;
use App\Helpers\CreateLog;
use App\Helpers\OrganizeResponse;
use App\Http\Requests\TransferRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferValueService
{
    private UserRepository $userRepository;
    private GetTransferAccountsByIdService $service;
    private AuthorizationClient $client;

    public function __construct(
        UserRepository $userRepository,
        GetTransferAccountsByIdService $service,
        AuthorizationClient $client
    )
    {
        $this->userRepository = $userRepository;
        $this->service = $service;
        $this->client = $client;
    }

    /**
     * @param TransferRequest $request
     * @return OrganizeResponse
     * @throws \Throwable
     */
    public function transferValue(TransferRequest $request): OrganizeResponse
    {
        try {
            if (!$this->checkTransferAuthorization()) {
                return new OrganizeResponse(403, 'Transferência não autorizada!');
            }

            DB::beginTransaction();
            $accounts = $this->service->getTransferAccountsById($request);
            $this->checkAccountBalance($accounts['originAccount'], $request);
            $this->userRepository->transferValue(
                $accounts['originAccount'],
                $accounts['destinationAccount'],
                $request->value
            );
            DB::commit();

            return new OrganizeResponse(
                200,
                'Transferência realizada com sucesso!'
            );
        } catch (\DomainException $exception) {
            DB::rollBack();
            CreateLog::logError($exception->getMessage(), $exception->getFile(), $exception->getLine());
            return new OrganizeResponse(500, $exception->getMessage());
        } catch (\Throwable $exception) {
            DB::rollBack();
            CreateLog::logError($exception->getMessage(), $exception->getFile(), $exception->getLine());
            return new OrganizeResponse(500, 'Ocorreu um erro ao processar a transferência!');
        }
    }

    /**
     * @param User $originAccount
     * @param TransferRequest $request
     * @return void
     */
    private function checkAccountBalance(User $originAccount, TransferRequest $request): void
    {
        if ($originAccount->balance < $request->value) {
            throw new \DomainException('Saldo insuficiente para realizar a transferência!');
        }
    }

    private function checkTransferAuthorization(): bool
    {
        return $this->client->checkAuthorization();
    }
}
