<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

use Money\Money;

class Rounding
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting
     */
    private $pricingSetting;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\PricingSetting $pricingSetting
     */
    public function __construct(PricingSetting $pricingSetting)
    {
        $this->pricingSetting = $pricingSetting;
    }

    /**
     * @param string|\Money\Money $priceWithVat
     * @return string|\Money\Money
     */
    public function roundPriceWithVat($priceWithVat)
    {
        $roundingType = $this->pricingSetting->getRoundingType();

        if ($priceWithVat instanceof Money) {
            $roundingSubUnitAmounts = [
                PricingSetting::ROUNDING_TYPE_HUNDREDTHS => 1,
                PricingSetting::ROUNDING_TYPE_FIFTIES => 50,
                PricingSetting::ROUNDING_TYPE_INTEGER => 100,
            ];
            return $this->roundMoney($priceWithVat, new Money($roundingSubUnitAmounts[$roundingType], $priceWithVat->getCurrency()));
        }

        switch ($roundingType) {
            case PricingSetting::ROUNDING_TYPE_HUNDREDTHS:
                $roundedPriceWithVat = round($priceWithVat, 2);
                break;

            case PricingSetting::ROUNDING_TYPE_FIFTIES:
                $roundedPriceWithVat = round($priceWithVat * 2, 0) / 2;
                break;

            case PricingSetting::ROUNDING_TYPE_INTEGER:
                $roundedPriceWithVat = round($priceWithVat, 0);
                break;

            default:
                throw new \Shopsys\FrameworkBundle\Model\Pricing\Exception\InvalidRoundingTypeException(
                    sprintf('Rounding type %s is not valid', $roundingType)
                );
        }

        return $roundedPriceWithVat;
    }

    /**
     * @param string|\Money\Money $priceWithoutVat
     * @return string|\Money\Money
     */
    public function roundPriceWithoutVat($priceWithoutVat)
    {
        if ($priceWithoutVat instanceof Money) {
            return $priceWithoutVat;
        }

        return round($priceWithoutVat, 2);
    }

    /**
     * @param string|\Money\Money $vatAmount
     * @return string|\Money\Money
     */
    public function roundVatAmount($vatAmount)
    {
        if ($vatAmount instanceof Money) {
            return $vatAmount;
        }

        return round($vatAmount, 2);
    }

    /**
     * @param \Money\Money $money
     * @param \Money\Money $roundingAmount
     * @return \Money\Money
     */
    private function roundMoney(Money $money, Money $roundingAmount): Money
    {
        $modulo = $money->mod($roundingAmount);
        if ($modulo->greaterThanOrEqual($roundingAmount->divide(2))) {
            $money = $money->add($roundingAmount);
        }

        return $money->subtract($modulo);
    }
}
