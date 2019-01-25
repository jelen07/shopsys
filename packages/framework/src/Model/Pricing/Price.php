<?php

namespace Shopsys\FrameworkBundle\Model\Pricing;

use Money\Money;

class Price
{
    /**
     * @var bool
     */
    private $usingMoney;

    /**
     * @var string|\Money\Money
     */
    private $priceWithoutVat;

    /**
     * @var string|\Money\Money
     */
    private $priceWithVat;

    /**
     * @var string|\Money\Money
     */
    private $vatAmount;

    /**
     * @param string|\Money\Money $priceWithoutVat
     * @param string|\Money\Money $priceWithVat
     */
    public function __construct($priceWithoutVat, $priceWithVat)
    {
        $this->usingMoney = $priceWithVat instanceof Money && $priceWithoutVat instanceof Money;
        $this->priceWithoutVat = $priceWithoutVat;
        $this->priceWithVat = $priceWithVat;
        $this->vatAmount = $this->usingMoney ? $priceWithVat->subtract($priceWithoutVat) : $priceWithVat - $priceWithoutVat;
    }

    /**
     * @return string|\Money\Money
     */
    public function getPriceWithoutVat()
    {
        return $this->priceWithoutVat;
    }

    /**
     * @return string|\Money\Money
     */
    public function getPriceWithVat()
    {
        return $this->priceWithVat;
    }

    /**
     * @return string|\Money\Money
     */
    public function getVatAmount()
    {
        return $this->vatAmount;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $priceToAdd
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function add(self $priceToAdd)
    {
        $priceToAdd = $this->convertToSameFormat($priceToAdd);

        return new self(
            $this->usingMoney ? $this->priceWithoutVat->add($priceToAdd->priceWithoutVat) : $this->priceWithoutVat + $priceToAdd->getPriceWithoutVat(),
            $this->usingMoney ? $this->priceWithVat->add($priceToAdd->priceWithVat) : $this->priceWithVat + $priceToAdd->getPriceWithVat()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $priceToSubtract
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    public function subtract(self $priceToSubtract)
    {
        $priceToSubtract = $this->convertToSameFormat($priceToSubtract);

        return new self(
            $this->usingMoney ? $this->priceWithoutVat->subtract($priceToSubtract->priceWithoutVat) : $this->priceWithoutVat - $priceToSubtract->getPriceWithoutVat(),
            $this->usingMoney ? $this->priceWithVat->subtract($priceToSubtract->priceWithVat) : $this->priceWithVat - $priceToSubtract->getPriceWithVat()
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Price $price
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Price
     */
    private function convertToSameFormat(self $price): self
    {
        if ($this->usingMoney && !$price->usingMoney) {
            $price = new self(
                new Money($price->priceWithoutVat * 100, $this->priceWithoutVat->getCurrency()),
                new Money($price->priceWithVat * 100, $this->priceWithVat->getCurrency())
            );
        }
        if (!$this->usingMoney && $price->usingMoney) {
            $price = new self(
                $price->priceWithoutVat->getAmount() / 100,
                $price->priceWithVat->getAmount() / 100
            );
        }
        return $price;
    }
}
