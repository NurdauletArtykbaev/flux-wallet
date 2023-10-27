<?php

namespace Nurdaulet\FluxWallet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BankcardsResource extends JsonResource
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
            'id'            => $this->id,
            'number'        => $this->number,
            'month'         => $this->month,
            'year'          => $this->year,
            'bank'          => $this->bank,
            'country'       => $this->country,
            'card_id'       => $this->card_id,
            'card_owner'    => $this->card_owner,
        ];
    }
}
