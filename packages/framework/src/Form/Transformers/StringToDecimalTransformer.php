<?php

namespace Shopsys\FrameworkBundle\Form\Transformers;

use Shopsys\FrameworkBundle\Component\Decimal\Decimal;
use Symfony\Component\Form\DataTransformerInterface;

class StringToDecimalTransformer implements DataTransformerInterface
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal[]|\Shopsys\FrameworkBundle\Component\Decimal\Decimal|null $value
     * @return string[]|string|null
     */
    public function transform($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'transformSingleValue'], $value);
        }

        return $this->transformSingleValue($value);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal|null $value
     * @return string|null
     */
    public function transformSingleValue($value)
    {
        return $value !== null ? (string)$value : null;
    }

    /**
     * @param mixed $value
     * @param string[]|string|null
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal[]|\Shopsys\FrameworkBundle\Component\Decimal\Decimal|null $value
     */
    public function reverseTransform($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'reverseTransformSingleValue'], $value);
        }

        return $this->reverseTransformSingleValue($value);
    }

    /**
     * @param mixed $value
     * @param string|null
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal|null $value
     */
    public function reverseTransformSingleValue($value)
    {
        return $value !== null ? Decimal::fromString($value) : null;
    }
}
