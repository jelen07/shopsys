<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Litipk\BigNumbers\Decimal;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;

interface PaymentPriceFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Litipk\BigNumbers\Decimal $price
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentPrice
     */
    public function create(
        Payment $payment,
        Currency $currency,
        Decimal $price
    ): PaymentPrice;
}
