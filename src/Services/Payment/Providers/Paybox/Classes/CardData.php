<?php

namespace Nurdaulet\FluxWallet\Services\Payment\Providers\Paybox\Classes;

class CardData
{
    public string $hash;
    public string $month;
    public string $bank;
    public string $country;
    public string $year;
    public string $status;
    public string $cardId;
    public string | null  $recurringProfileId ;
    public string $userId;
    public string $has3ds;
    public  $cityId;

    public function __construct($data)
    {
        $this->hash               = $data->pg_card_hash;
        $this->month              = $data->pg_card_month;
        $this->bank               = $data->pg_bank;
        $this->country            = $data->pg_country;
        $this->year               = $data->pg_card_year;
        $this->status             = $data->pg_status;
        $this->cardId             = $data->pg_card_id;
        $this->recurringProfileId = is_object($data->pg_recurring_profile_id)  ? null :  $data->pg_recurring_profile_id ?? null;
        $this->userId             = $data->pg_user_id;
        $this->has3ds             = $data->pg_card_3ds;
        $this->cityId             =   is_object($data->pg_order_id)  ? null :  $data->pg_order_id ?? null;
    }

    public function toModel()
    {
        return [
            'number'                => $this->hash,
            'month'                 => $this->month,
            'year'                  => $this->year,
            'user_id'               => $this->userId,
            'card_id'               => (int) $this->cardId,
            'recurring_profile_id'  => $this->recurringProfileId,
            'has_3ds'               => (bool) $this->has3ds,
            'bank'                  => $this->bank,
            'status'                => $this->status,
            'country'               => $this->country,
            'city_id'               => $this->cityId,
            'provider'              => 'paybox'
        ];
    }
}
