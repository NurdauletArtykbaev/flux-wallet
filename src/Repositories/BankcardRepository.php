<?php

namespace Nurdaulet\FluxWallet\Repositories;

use Nurdaulet\FluxWallet\Filters\BankcardFilter;

class BankcardRepository
{

    public function find($id, $filers = [])
    {
        return config('flux-wallet.models.bankcard')::query()->applyFilters(new BankcardFilter(), $filers)->findOrFail($id);
    }

    public function getByUser($user)
    {
        return config('flux-wallet.models.bankcard')::query()->where('user_id', $user->id)->get();
    }

    public function firstOrCreate($conditions = [], $data = [])
    {
        return config('flux-wallet.models.bankcard')::firstOrCreate($conditions, $data);
    }
}
