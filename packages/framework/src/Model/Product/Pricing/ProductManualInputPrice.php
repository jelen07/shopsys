<?php

namespace Shopsys\FrameworkBundle\Model\Product\Pricing;

use Doctrine\ORM\Mapping as ORM;
use Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation;
use Shopsys\FrameworkBundle\Model\Product\Product;

/**
 * @ORM\Table(name="product_manual_input_prices")
 * @ORM\Entity
 */
class ProductManualInputPrice
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Product\Product")
     * @ORM\JoinColumn(nullable=false, name="product_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Product
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup")
     * @ORM\JoinColumn(nullable=false, name="pricing_group_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $pricingGroup;

    /**
     * @var string
     *
     * @ORM\Column(type="decimal", precision=20, scale=6, nullable=true)
     */
    protected $inputPrice;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param string|null $inputPrice
     */
    public function __construct(Product $product, PricingGroup $pricingGroup, $inputPrice)
    {
        $this->product = $product;
        $this->pricingGroup = $pricingGroup;
        $this->inputPrice = $inputPrice;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Product\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup
     */
    public function getPricingGroup()
    {
        return $this->pricingGroup;
    }

    /**
     * @return string
     */
    public function getInputPrice()
    {
        return $this->inputPrice;
    }

    /**
     * @param string $inputPrice
     */
    public function setInputPrice($inputPrice)
    {
        $this->inputPrice = $inputPrice;
    }

    /**
     * @param int $inputPriceType
     * @param string $newVatPercent
     * @param \Shopsys\FrameworkBundle\Model\Pricing\BasePriceCalculation $basePriceCalculation
     * @param \Shopsys\FrameworkBundle\Model\Pricing\InputPriceCalculation $inputPriceCalculation
     */
    public function recalculateInputPriceForNewVatPercent(
        $inputPriceType,
        $newVatPercent,
        BasePriceCalculation $basePriceCalculation,
        InputPriceCalculation $inputPriceCalculation
    ) {
        $basePriceForPricingGroup = $basePriceCalculation->calculateBasePrice(
            $this->getInputPrice(),
            $inputPriceType,
            $this->getProduct()->getVat()
        );
        $inputPriceForPricingGroup = $inputPriceCalculation->getInputPrice(
            $inputPriceType,
            $basePriceForPricingGroup->getPriceWithVat(),
            $newVatPercent
        );
        $this->setInputPrice($inputPriceForPricingGroup);
    }
}
