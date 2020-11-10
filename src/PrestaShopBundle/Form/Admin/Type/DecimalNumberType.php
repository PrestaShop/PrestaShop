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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Currency;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Currency\CurrencyDataProviderInterface;
use PrestaShopBundle\Form\DataTransformer\DecimalNumberToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class DecimalNumberType extends NumberType
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var CurrencyDataProviderInterface
     */
    private $currencyDataProvider;

    public function __construct(
        LegacyContext $legacyContext,
        CurrencyDataProviderInterface $currencyDataProvider
    ) {
        $this->legacyContext = $legacyContext;
        $this->currencyDataProvider = $currencyDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $scale = !empty($options['scale']) ? $options['scale'] : $this->getCurrencyPrecision();

        $builder->addViewTransformer(new DecimalNumberToLocalizedStringTransformer(
            $scale,
            $options['grouping'],
            $options['rounding_mode']
        ), true);
    }

    /**
     * Get precision from context or default currency
     *
     * @return int
     */
    private function getCurrencyPrecision(): int
    {
        if ($this->legacyContext->getContext()->currency instanceof Currency) {
            $currency = $this->legacyContext->getContext()->currency;
        } else {
            $defaultIso = $this->currencyDataProvider->getDefaultCurrencyIsoCode();
            $currency = $this->currencyDataProvider->getCurrencyByIsoCode($defaultIso);
        }

        return (int) $currency->precision;
    }
}
