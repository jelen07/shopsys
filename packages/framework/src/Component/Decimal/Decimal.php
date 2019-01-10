<?php

namespace Shopsys\FrameworkBundle\Component\Decimal;

use Litipk\BigNumbers\Decimal as InnerDecimal;

class Decimal
{
    private const SCALE_INNER = 32;
    private const SCALE_TO_STRING = 16;

    /**
     * @var \Litipk\BigNumbers\Decimal
     */
    private $inner;

    /**
     * @param \Litipk\BigNumbers\Decimal $inner
     */
    private function __construct(InnerDecimal $inner)
    {
        $this->inner = $inner;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal|string|int|float $value
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public static function create($value): self
    {
        if ($value instanceof self) {
            $value = $value->inner;
        }

        return new self(InnerDecimal::create($value, self::SCALE_INNER));
    }

    /**
     * @param string $string
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public static function fromString(string $string): self
    {
        return new self(InnerDecimal::fromString($string, self::SCALE_INNER));
    }

    /**
     * @param int $integer
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public static function fromInt(int $integer): self
    {
        return new self(InnerDecimal::fromInteger($integer));
    }

    /**
     * @param float $float
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public static function fromFloat(float $float): self
    {
        return new self(InnerDecimal::fromFloat($float, self::SCALE_INNER));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $decimal
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public function add(self $decimal): self
    {
        return new self($this->inner->add($decimal->inner, self::SCALE_INNER));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $decimal
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public function subtract(self $decimal): self
    {
        return new self($this->inner->sub($decimal->inner, self::SCALE_INNER));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $decimal
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public function divide(self $decimal): self
    {
        return new self($this->inner->div($decimal->inner, self::SCALE_INNER));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $decimal
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public function multiply(self $decimal): self
    {
        return new self($this->inner->mul($decimal->inner, self::SCALE_INNER));
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $decimal
     * @return bool
     */
    public function equals(self $decimal): bool
    {
        return $this->inner->equals($decimal->inner, self::SCALE_INNER);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $decimal
     * @return bool
     */
    public function isGreaterThan(self $decimal): bool
    {
        return $this->inner->isGreaterThan($decimal->inner, self::SCALE_INNER);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $decimal
     * @return bool
     */
    public function isGreaterOrEqualTo(self $decimal): bool
    {
        return $this->inner->isGreaterOrEqualTo($decimal->inner, self::SCALE_INNER);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $decimal
     * @return bool
     */
    public function isLessThan(self $decimal): bool
    {
        return $this->inner->isLessThan($decimal->inner, self::SCALE_INNER);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Component\Decimal\Decimal $decimal
     * @return bool
     */
    public function isLessOrEqualTo(self $decimal): bool
    {
        return $this->inner->isLessOrEqualTo($decimal->inner, self::SCALE_INNER);
    }

    /**
     * @param int $precision
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public function round(int $precision = 0): self
    {
        return new self($this->inner->round($precision));
    }

    /**
     * @param int $precision
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public function floor(int $precision = 0): self
    {
        return new self($this->inner->floor($precision));
    }

    /**
     * @param int $precision
     * @return \Shopsys\FrameworkBundle\Component\Decimal\Decimal
     */
    public function ceil(int $precision = 0): self
    {
        return new self($this->inner->ceil($precision));
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->inner->round(self::SCALE_TO_STRING);
    }
}
