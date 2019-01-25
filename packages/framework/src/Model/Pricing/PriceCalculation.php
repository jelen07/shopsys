<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

use Money\Money;
use Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat;

class PriceCalculation
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Rounding
     */
    private $rounding;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Rounding $rounding
     */
    public function __construct(Rounding $rounding)
    {
        $this->rounding = $rounding;
    }

    /**
     * @param string|\Money\Money $priceWithVat
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @return string|\Money\Money
     */
    public function getVatAmountByPriceWithVat($priceWithVat, Vat $vat)
    {
        if ($priceWithVat instanceof Money) {
            return $this->rounding->roundVatAmount(
                $priceWithVat->multiply($this->getVatCoefficientByPercent($vat->getPercent()))
            );
        }

        return $this->rounding->roundVatAmount(
            $priceWithVat * $this->getVatCoefficientByPercent($vat->getPercent())
        );
    }

    /**
     * @param string $vatPercent
     * @return string
     */
    public function getVatCoefficientByPercent($vatPercent)
    {
        $ratio = $vatPercent / (100 + $vatPercent);
        return round($ratio, 4);
    }

    /**
     * @param string|\Money\Money $priceWithoutVat
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat $vat
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Vat\Vat
     * @return string|\Money\Money
     */
    public function applyVatPercent($priceWithoutVat, Vat $vat)
    {
        if ($priceWithoutVat instanceof Money) {
            return $priceWithoutVat->multiply((100 + $vat->getPercent()) / 100);
        }

        return $priceWithoutVat * (100 + $vat->getPercent()) / 100;
    }
}
