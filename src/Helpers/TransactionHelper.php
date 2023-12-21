<?php

namespace Nurdaulet\FluxWallet\Helpers;


class TransactionHelper
{

    const TYPE_REGISTER = 'bonus_for_register';
    const TYPE_ORDER = 'order';
    const TYPE_FIRST_ORDER = 'first_order';
    const TYPE_TOP_UP = 'top_up';
    const TYPE_ADD_CARD = 'add_card';
    const TYPE_NOT_DEFINED = 'not_defined';

    public static function getTransactionText($type, $fieldsJson, $amount)
    {

        if ($type == TransactionHelper::TYPE_REGISTER) {
            $text = trans("admin.transactions.$type");
        } else if ($type == TransactionHelper::TYPE_ORDER) {
            $text = trans("admin.transactions.$type", ['id' => $fieldsJson['order_id'] ?? '?']);
        } else if ($type == TransactionHelper::TYPE_FIRST_ORDER) {
            $text = trans("admin.transactions.$type", ['amount' => BonusHelper::BONUS_FIRST_ORDER, 'company_name' => $fieldsJson['company_name']?? '?']);
        } else if ($type == TransactionHelper::TYPE_TOP_UP) {
            $text = trans("admin.transactions.$type",['amount' => $amount ?? '?']);
        } else {
            $text = trans("admin.transactions.not_recognized");
        }
        return $text;
    }

}
