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
use PrestaShop\PrestaShop\Core\Localization\Locale;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CustomMoneyType extends AbstractTypeExtension
{
    public const PRESTASHOP_DECIMALS = 6;

    protected const PATTERN_MAP = [
        "¤\u{a0}#,##0.00" => "¤\u{a0}{{ widget }}",
        '¤#,##0.00' => '¤{{ widget }}',
        "#,##0.00\u{a0}¤" => "{{ widget }}\u{a0}¤",
        '#,##0.00¤' => '{{ widget }}¤',
        "-¤\u{a0}#,##0.00" => "-¤\u{a0}{{ widget }}",
        '-¤#,##0.00' => '-¤{{ widget }}',
        "-#,##0.00\u{a0}¤" => "-{{ widget }}\u{a0}¤",
        '-#,##0.00¤' => '-{{ widget }}¤',
    ];

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
        $pricePattern = $value->isPositive() ? $priceSpecification->getPositivePattern() : $priceSpecification->getNegativePattern();

        // inspired by symfony MoneyType
        preg_match('/^([^\s\xc2\xa0]*)[\s\xc2\xa0]*{{ widget }}(?:[,.]0+)?[\s\xc2\xa0]*([^\s\xc2\xa0]*)$/u', $moneyPattern, $matches);

        if (!empty($matches[1])) {
            $symbol = trim($matches[1]);
        }

        if (!isset($this::PATTERN_MAP[$pricePattern])) {
            return;
        }

        $view->vars['money_pattern'] = str_replace('¤', $symbol ?? $currency, $this::PATTERN_MAP[$pricePattern]);
    }
}
