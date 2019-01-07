<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Litipk\BigNumbers\Decimal;
use Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;

class PaymentPriceFactory implements PaymentPriceFactoryInterface
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver
     */
    protected $entityNameResolver;

    /**
     * @param \Shopsys\FrameworkBundle\Component\EntityExtension\EntityNameResolver $entityNameResolver
     */
    public function __construct(EntityNameResolver $entityNameResolver)
    {
        $this->entityNameResolver = $entityNameResolver;
    }

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
    ): PaymentPrice {
        $classData = $this->entityNameResolver->resolve(PaymentPrice::class);

        return new $classData($payment, $currency, $price);
    }
}
