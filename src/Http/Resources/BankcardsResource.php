<?php

namespace Nurdaulet\FluxWallet\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

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
        $cardType = $this->card_type ?? $this->getCardType();
        return [
            'id'             => $this->id,
            'number'         => $this->number,
            'month'          => $this->month,
            'year'           => $this->year,
            'bank'           => $this->bank,
            'country'        => $this->country,
            'card_id'        => $this->card_id,
            'card_type'      => $cardType,
            'card_type_icon' => $this->getCardTypeIcon($cardType),
            'card_owner'     => $this->card_owner,
        ];
    }

    private function getCardType()
    {
        if ($this->card_type) {
            return $this->card_type;
        }
        if (preg_match('/^4/', $this->number)) {
            return "Visa";
        } elseif (preg_match('/^5[1-5]/', $this->number)) {
            return "MasterCard";
        } elseif (preg_match('/^3[47]/', $this->number)) {
            return "American Express";
        } elseif (preg_match('/^3(?:0[0-5]|[68][0-9])/', $this->number)) {
            return "Diners Club";
        } elseif (preg_match('/^6(?:011|5[0-9]{1})/', $this->number)) {
            return "Discover";
        } elseif (preg_match('/^(?:2131|1800|35\d{2})/', $this->number)) {
            return "JCB";
        }
        return null;
    }

    private function getCardTypeIcon($cardType): ?string
    {
        if (empty($cardType)) {
            return null;
        }

        if (Str::lower($cardType) == 'visa') {
            return env('APP_URL')   . '/vendor/flux-wallet/icons/visa.svg';
        }
        if (Str::lower($cardType) == 'mastercard') {
            return env('APP_URL')   . '/vendor/flux-wallet/icons/master-card.svg';
        }
        return null;
    }
}
