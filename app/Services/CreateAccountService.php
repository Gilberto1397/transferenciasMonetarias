<?php

namespace App\Services;

use App\Contracts\FisicAccountRepository;
use App\Contracts\JuristicAccountRepository;
use App\Contracts\UserRepository;
use App\Helpers\CreateLog;
use App\Helpers\OrganizeResponse;
use App\Http\Requests\CreateAccountRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateAccountService
{
    private JuristicAccountRepository $accountRepository;
    private FisicAccountRepository $fisicAccountRepository;
    private UserRepository $userRepository;

    /**
     * @param JuristicAccountRepository $accountRepository
     * @param FisicAccountRepository $fisicAccountRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        JuristicAccountRepository $accountRepository,
        FisicAccountRepository    $fisicAccountRepository,
        UserRepository            $userRepository
    )
    {
        $this->accountRepository = $accountRepository;
        $this->fisicAccountRepository = $fisicAccountRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param CreateAccountRequest $request
     * @return OrganizeResponse
     * @throws \Throwable
     */
    public function createAccount(CreateAccountRequest $request): OrganizeResponse
    {
        try {
            DB::beginTransaction();
            $user = $this->userRepository->createUser($request);

            if (!$this->chooseAccount($request, $user)) {
                throw new \DomainException('Erro ao criar conta bancária!');
            }
            DB::commit();
            return new OrganizeResponse(201, 'Conta criada com sucesso!');
        } catch (\DomainException $exception) {
            DB::rollBack();
            CreateLog::logError($exception->getMessage(), $exception->getFile(), $exception->getLine(), $request->all());
            throw $exception;
        } catch (\Throwable $exception) {
            DB::rollBack();
            CreateLog::logError($exception->getMessage(), $exception->getFile(), $exception->getLine(), $request->all());
            throw new \DomainException('Houve um erro ao criar conta bancária!');
        }
    }

    private function chooseAccount(CreateAccountRequest $request, User $user): bool
    {
        if ($request->tipoConta === 1) {
            return $this->accountRepository->createAccount($request, $user);
        }
        return $this->fisicAccountRepository->createAccount($request, $user);
    }
}
