<?php

namespace Nurdaulet\FluxWallet\Http\Controllers;

use Nurdaulet\FluxWallet\Http\Requests\TransactionRequest;
use Nurdaulet\FluxWallet\Http\Resources\TransactionsResource;
use Nurdaulet\FluxWallet\Services\TransactionService;

class TransactionController
{
    public function __construct(private TransactionService $transactionService)
    {
    }

    public function index(TransactionRequest $request)
    {
        $user = $request->user();
        $transactions = $this->transactionService->get($user, $request->validated());

        return TransactionsResource::collection($transactions);
    }
}
