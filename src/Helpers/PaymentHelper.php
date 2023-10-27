<?php

namespace Nurdaulet\FluxWallet\Helpers;


class PaymentHelper
{
    const STATUS_PENDING = 1;
    const STATUS_PAID = 2;
    const STATUS_FAILED = 3;
    const STATUS_REFUND = 4;
    const STATUS_REFUND_FAILED = 'refund_failed';


    const ONLINE_EPAY           = 1;
    const ONLINE_CLOUD_PAYMENTS = 12;


    public static function isPaymentMethodOnline($paymentMethodId): bool
    {
        return in_array($paymentMethodId, [PaymentHelper::ONLINE_EPAY]);
    }
}
