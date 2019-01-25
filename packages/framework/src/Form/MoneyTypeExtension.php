<?php

namespace Shopsys\FrameworkBundle\Form;

use CommerceGuys\Intl\Currency\CurrencyRepositoryInterface;
use Money\Currencies\CurrencyList;
use Money\Currency;
use Money\Exception\FormatterException;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\MoneyFormatter;
use Money\MoneyParser;
use Money\Parser\DecimalMoneyParser;
use Shopsys\FrameworkBundle\Form\Transformers\RemoveWhitespacesTransformer;
use Shopsys\FrameworkBundle\Model\Localization\Localization;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class MoneyTypeExtension extends AbstractTypeExtension
{
    /**
     * @var \Shopsys\FrameworkBundle\Model\Localization\Localization
     */
    private $localization;

    /**
     * @var \CommerceGuys\Intl\Currency\CurrencyRepositoryInterface
     */
    private $intlCurrencyRepository;

    /**
     * @param \Shopsys\FrameworkBundle\Model\Localization\Localization $localization
     * @param \CommerceGuys\Intl\Currency\CurrencyRepositoryInterface $intlCurrencyRepository
     */
    public function __construct(Localization $localization, CurrencyRepositoryInterface $intlCurrencyRepository)
    {
        $this->localization = $localization;
        $this->intlCurrencyRepository = $intlCurrencyRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currencies = new CurrencyList(['CZK'=>2,'EUR'=>2,'USD'=>2]);
        $currency = new Currency($options['currency']);

        $builder->addViewTransformer(new RemoveWhitespacesTransformer());
        $builder->addModelTransformer(new class(new DecimalMoneyFormatter($currencies), new DecimalMoneyParser($currencies), $currency) implements DataTransformerInterface {
            /** @var \Money\MoneyFormatter */
            private $formatter;
            /** @var \Money\MoneyParser */
            private $parser;
            /** @var \Money\Currency */
            private $currency;

            public function __construct(MoneyFormatter $formatter, MoneyParser $parser, Currency $currency)
            {
                $this->formatter = $formatter;
                $this->parser = $parser;
                $this->currency = $currency;
            }

            /**
             * @param \Money\Money|null $value
             * @return string
             */
            public function transform($value)
            {
                if ($value === null) {
                    return '';
                } elseif ($value instanceof Money) {
                    if (!$value->getCurrency()->equals($this->currency)) {
                        throw new \Symfony\Component\Form\Exception\TransformationFailedException('Currency mišmaš.');
                    }
                    try {
                        return $this->formatter->format($value);
                    } catch (FormatterException $e) {
                        throw new \Symfony\Component\Form\Exception\TransformationFailedException('Formatting problem', 0, $e);
                    }
                }
                throw new \Symfony\Component\Form\Exception\TransformationFailedException('Wrong type');
            }

            /**
             * @param mixed $value
             * @return \Money\Money|null
             */
            public function reverseTransform($value)
            {
                if ($value === null || $value = '') {
                    return null;
                } elseif (is_string($value)) {
                    try {
                        return $this->parser->parse($value, $this->currency);
                    } catch (FormatterException $e) {
                        throw new \Symfony\Component\Form\Exception\TransformationFailedException('Parsing problem', 0, $e);
                    }
                }
                throw new \Symfony\Component\Form\Exception\TransformationFailedException('Wrong type');
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['money_pattern'] = $this->getPattern($options['currency']);
    }

    /**
     * @return string
     */
    public function getExtendedType()
    {
        return MoneyType::class;
    }

    /**
     * Returns the pattern for this locale. Always places currency symbol after widget.
     * The pattern contains the placeholder "{{ widget }}" where the HTML tag should be inserted
     * @see \Symfony\Component\Form\Extension\Core\Type\MoneyType::getPattern()
     * @param string|bool $currency
     * @return string
     */
    private function getPattern($currency)
    {
        if (!$currency) {
            return '{{ widget }}';
        } else {
            $intlCurrency = $this->intlCurrencyRepository->get($currency, $this->localization->getLocale());

            return '{{ widget }} ' . $intlCurrency->getSymbol();
        }
    }
}
