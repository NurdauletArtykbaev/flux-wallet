<?php

namespace Nurdaulet\FluxWallet\Services;

use Nurdaulet\FluxWallet\Helpers\PaymentHelper;
use Nurdaulet\FluxWallet\Helpers\TransactionHelper;

class BalanceService
{
    public function payOrderIfUsedBonus($isUsedBonus, $user, $order, $totalPrice): void
    {
        if ($isUsedBonus == 'true' && $user->balance && $user->balance->bonus > 0) {
            if ($user->balance->bonus) {
                $amount = $user->balance->bonus;
                if ($totalPrice >= $user->balance->bonus) {
                    $bonus = 0;
                } else {
                    $bonus = $user->balance->bonus - $totalPrice;
                    $amount = $user->balance->bonus - $bonus;
                }
                $user->balance()->update([
                    'bonus' => $bonus
                ]);
                $user->transactions()->create([
                    'fields_json' => ['id' => $order->id],
                    'type' => TransactionHelper::TYPE_ORDER,
                    'bonus' => $user->balance->bonus,
                    'money' => $user->balance->money,
                    'status' => PaymentHelper::STATUS_PAID,
                    'amount' => $amount,
                ]);
            }
        }
    }

    public function update($user, $money = null, $bonus = null)
    {
        $data = [];
        if ($bonus) {
            $data['bonus'] = $bonus;
        }
        if ($money) {
            $data['money'] = $money;
        }
        $user->balance()->update($data);
    }

    public function create($user)
    {
        $user->balance()->updateOrCreate([
        ],[
            'bonus' => 0
        ]);
//        $user->transactions()->create([
//            'type' => TransactionHelper::TYPE_REGISTER,
//            'is_replenish' => true,
//            'amount' => BonusHelper::REGISTER_BONUS,
//            'status' => PaymentHelper::STATUS_PAID
//        ]);

        return $user;
    }

    public function calculateOrderTotalPriceIfUsedBonus($isUsedBonus, $user, $totalPrice)
    {
        if ($isUsedBonus == 'true' && $user->balance && $user->balance->bonus > 0) {
            if ($user->balance->bonus) {
                $usedBonus = $user->balance->bonus;
                if ($totalPrice >= $user->balance->bonus) {
                    $totalPrice = $totalPrice - $user->balance->bonus;
                } else {
                    $bonus = $user->balance->bonus - $totalPrice;
                    $usedBonus = $user->balance->bonus - $bonus;
                    $totalPrice = 0;
                }
                return [$totalPrice, $usedBonus];

            }
        }
        return [$totalPrice, 0];
    }
}
