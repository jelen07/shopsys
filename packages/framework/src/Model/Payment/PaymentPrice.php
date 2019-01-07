<?php

namespace Shopsys\FrameworkBundle\Model\Payment;

use Doctrine\ORM\Mapping as ORM;
use Litipk\BigNumbers\Decimal;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;

/**
 * @ORM\Table(name="payment_prices")
 * @ORM\Entity
 */
class PaymentPrice
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Payment\Payment
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Payment\Payment", inversedBy="prices")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $payment;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $currency;

    /**
     * @var \Litipk\BigNumbers\Decimal
     *
     * @ORM\Column(type="decimal_object", precision=20, scale=6)
     */
    protected $price;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Payment\Payment $payment
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @param string $price
     */
    public function __construct(Payment $payment, Currency $currency, $price)
    {
        $this->payment = $payment;
        $this->currency = $currency;
        $this->price = $price;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Payment\Payment
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @return \Litipk\BigNumbers\Decimal
     */
    public function getPrice(): Decimal
    {
        return $this->price;
    }

    /**
     * @param \Litipk\BigNumbers\Decimal $price
     */
    public function setPrice(Decimal $price)
    {
        $this->price = $price;
    }
}
