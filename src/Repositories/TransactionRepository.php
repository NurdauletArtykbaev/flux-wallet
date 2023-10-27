<?php

namespace Nurdaulet\FluxWallet\Repositories;

use Nurdaulet\FluxWallet\Filters\TransactionFilter;

class TransactionRepository
{
    public function get($filters = [])
    {
        return config('flux-wallet.models.transaction')::latest('id')
            ->applyFilters(new TransactionFilter(), $filters)
            ->latest()
            ->get();
    }
}
