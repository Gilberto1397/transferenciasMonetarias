<?php

namespace App\Services;

use App\Clients\AuthorizationClient;
use App\Clients\NotificationClient;
use App\Contracts\UserRepository;
use App\Helpers\CreateLog;
use App\Helpers\OrganizeResponse;
use App\Http\Requests\TransferRequest;
use App\Jobs\NotifyUserJob;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TransferValueService
{
    private UserRepository $userRepository;
    private GetTransferAccountsByIdService $service;
    private AuthorizationClient $authorizationClient;

    public function __construct(
        UserRepository                 $userRepository,
        GetTransferAccountsByIdService $service,
        AuthorizationClient            $authorizationClient,
    )
    {
        $this->userRepository = $userRepository;
        $this->service = $service;
        $this->authorizationClient = $authorizationClient;
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
            $this->checkAccountBalance($accounts['payer'], $request);
            $this->userRepository->transferValue(
                $accounts['payer'],
                $accounts['payee'],
                $request->value
            );
            DB::commit();

            dispatch(new NotifyUserJob());
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
     * @param User $payer
     * @param TransferRequest $request
     * @return void
     */
    private function checkAccountBalance(User $payer, TransferRequest $request): void
    {
        if ($payer->balance < $request->value) {
            throw new \DomainException('Saldo insuficiente para realizar a transferência!');
        }
    }

    private function checkTransferAuthorization(): bool
    {
        return $this->authorizationClient->checkAuthorization();
    }
}
