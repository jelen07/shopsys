<?php

namespace Shopsys\FrameworkBundle\Component\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use Shopsys\FrameworkBundle\Component\Decimal\Decimal;

class DecimalObjectType extends Type
{
    /**
     * {@inheritDoc}
     */
    public function getName(): string
    {
        return 'decimal_object';
    }

    /**
     * {@inheritDoc}
     */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return $platform->getDecimalTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * {@inheritDoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof Decimal) {
            return (string)$value;
        }

        throw ConversionException::conversionFailedInvalidType($value, $this->getName(), ['null', Decimal::class]);
    }

    /**
     * {@inheritDoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?Decimal
    {
        if ($value === null) {
            return null;
        }

        try {
            return Decimal::fromString($value);
        } catch (\Exception $e) {
            $expectedFormat = \Litipk\BigNumbers\Decimal::CLASSIC_DECIMAL_NUMBER_REGEXP;

            throw ConversionException::conversionFailedFormat($value, $this->getName(), $expectedFormat, $e);
        }
    }
}
