<?php

namespace Nurdaulet\FluxWallet\Http\Resources;

use Nurdaulet\FluxWallet\Helpers\TransactionHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'is_money' => $this->is_money,
            'is_replenish' => $this->is_replenish,
            'created_at' => $this->created_at?->format('d.m.Y'),
            'text' => TransactionHelper::getTransactionText($this->type,  $this->fields_json, $this->amount),
        ];
    }
}
