<?php


namespace Shopsys\FrameworkBundle\Model\Pricing;

use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;

class Money
{
    /**
     * @var \Money\Money
     */
    private $inner;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency
     */
    private $currency;

    /**
     * @param \Money\Money $inner
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     */
    private function __construct(\Money\Money $inner, Currency $currency)
    {
        $this->inner = $inner;
        $this->currency = $currency;
    }

    /**
     * @param int|string $amount
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency $currency
     * @return self
     */
    public static function create($amount, Currency $currency)
    {
        $innerCurrency = new \Money\Currency($currency->getCode());

        return new self(new \Money\Money($amount, $innerCurrency), $currency);
    }

    /**
     * @param self $money
     * @return self
     */
    public function add(self $money): self
    {
        return new self($this->inner->add($money->inner), $this->currency);
    }

    // ...
}
