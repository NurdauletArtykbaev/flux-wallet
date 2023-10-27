<?php

namespace Nurdaulet\FluxWallet\Traits;

use Nurdaulet\FluxWallet\Models\Transaction;

trait Billable
{
    public function getBillableId() {
        return $this->number;
    }


    public function transactions() {
        return $this->hasMany(config('flux-wallet.models.transaction')::class);
    }

//    public function transaction() {
//        return $this->morphOne(Transaction::class, 'transactionable');
//    }
//
//    public function isPaidTransaction() {
//        return $this->transaction()->isPaid()->where('fields_json->coast', $this->cost + (int) $this->delivery_cost)->first();
//    }
//
//    public function isPaid() {
//        return $this->transactions()->where('status', TransactionStatus::SUCCEED)->exists();
//    }
//
//    public function isPending() {
//        return $this->transactions()->where('status', TransactionStatus::PENDING)->exists();
//    }
}
