<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Type;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Localization\Currency\PatternTransformer;
use PrestaShop\PrestaShop\Core\Localization\Locale;
use PrestaShop\PrestaShop\Core\Localization\Specification\NumberInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomMoneyType extends AbstractTypeExtension
{
    public const PRESTASHOP_DECIMALS = 6;

    /**
     * @var Locale
     */
    private $locale;

    /**
     * @param Locale $locale
     */
    public function __construct(
        Locale $locale
    ) {
        $this->locale = $locale;
    }

    public static function getExtendedTypes(): iterable
    {
        return [MoneyType::class];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'precision' => null,
            'scale' => self::PRESTASHOP_DECIMALS,
            'grouping' => false,
            'divisor' => 1,
            'currency' => 'EUR',
            'compound' => false,
        ]);

        $resolver->setAllowedTypes('scale', 'int');
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $currency = $options['currency'];
        $moneyPattern = $view->vars['money_pattern'];

        $priceSpecification = $this->locale->getPriceSpecification($currency);
        $value = new DecimalNumber((string) (float) $view->vars['value']);

        // inspired by symfony MoneyType
        preg_match('/^([^\s\xc2\xa0]*)[\s\xc2\xa0]*{{ widget }}(?:[,.]0+)?[\s\xc2\xa0]*([^\s\xc2\xa0]*)$/u', $moneyPattern, $matches);

        if (!empty($matches[1])) {
            $symbol = trim($matches[1]);
        }

        $frameworkPattern = $this->getFrameworkPattern($priceSpecification, $value->isPositive());

        if (!$frameworkPattern) {
            return;
        }

        $view->vars['money_pattern'] = str_replace(PatternTransformer::CURRENCY_SYMBOL, $symbol ?? $currency, $frameworkPattern);
    }

    /**
     * @return string
     */
    protected function getFrameworkPattern(NumberInterface $priceSpecification, bool $isPositive): ?string
    {
        $patternTransformer = new PatternTransformer();
        $patternType = $patternTransformer->getTransformationType($priceSpecification->getPositivePattern());

        $positivePatternMap = [
            PatternTransformer::TYPE_LEFT_SYMBOL_WITH_SPACE => sprintf(
                '%s%s{{ widget }}',
                PatternTransformer::CURRENCY_SYMBOL,
                PatternTransformer::NO_BREAK_SPACE
            ),
            PatternTransformer::TYPE_RIGHT_SYMBOL_WITH_SPACE => sprintf(
                '{{ widget }}%s%s',
                PatternTransformer::NO_BREAK_SPACE,
                PatternTransformer::CURRENCY_SYMBOL
            ),
            PatternTransformer::TYPE_LEFT_SYMBOL_WITHOUT_SPACE => sprintf('%s{{ widget }}', PatternTransformer::CURRENCY_SYMBOL),
            PatternTransformer::TYPE_RIGHT_SYMBOL_WITHOUT_SPACE => sprintf('{{ widget }}%s', PatternTransformer::CURRENCY_SYMBOL),
        ];

        if (empty($positivePatternMap[$patternType])) {
            return null;
        }

        return $isPositive ? $positivePatternMap[$patternType] : sprintf('-%s', $positivePatternMap[$patternType]);
    }
}
