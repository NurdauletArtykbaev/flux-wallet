<?php

namespace Nurdaulet\FluxWallet\Services;

use Nurdaulet\FluxWallet\Repositories\TransactionRepository;

class TransactionService
{

    public function __construct(private TransactionRepository $transactionRepository)
    {
    }

    /**
     * @throws \Throwable
     */
    public function get($user, $filters = [])
    {
        $filters['from_date'] = $filters['from_date'] ?? now()->subMonth();
        $filters['to_date'] = $filters['to_date'] ?? now();
        $filters['user_id'] = $user->id;
//        $filters['user_id'] = $this->storeEmployeeService->getLordId($user);
        return $this->transactionRepository->get($filters);
    }



}
