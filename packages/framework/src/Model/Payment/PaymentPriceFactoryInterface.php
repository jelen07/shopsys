<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Shopsys\FrameworkBundle\Component\Decimal\Decimal;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;

interface PaymentPriceFactoryInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $price
     * @return \Shopsys\FrameworkBundle\Model\Payment\PaymentPrice
     */
    public function create(
        Payment $payment,
        Currency $currency,
        Decimal $price
    ): PaymentPrice;
}
