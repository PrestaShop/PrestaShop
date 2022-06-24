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

namespace PrestaShop\PrestaShop\Adapter\Product;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PageConfiguration is responsible for saving & loading product page configuration.
 */
class PageConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'display_quantities',
        'allow_add_variant_to_cart_from_listing',
        'attribute_anchor_separator',
        'display_discount_price',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'display_quantities' => (bool) $this->configuration->get('PS_DISPLAY_QTIES', false, $shopConstraint),
            'allow_add_variant_to_cart_from_listing' => (bool) $this->configuration->get('PS_ATTRIBUTE_CATEGORY_DISPLAY', false, $shopConstraint),
            'attribute_anchor_separator' => $this->configuration->get('PS_ATTRIBUTE_ANCHOR_SEPARATOR', null, $shopConstraint),
            'display_discount_price' => (bool) $this->configuration->get('PS_DISPLAY_DISCOUNT_PRICE', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        $errors = [];

        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_DISPLAY_QTIES', 'display_quantities', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_ATTRIBUTE_CATEGORY_DISPLAY', 'allow_add_variant_to_cart_from_listing', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_ATTRIBUTE_ANCHOR_SEPARATOR', 'attribute_anchor_separator', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_DISPLAY_DISCOUNT_PRICE', 'display_discount_price', $config, $shopConstraint);
        }

        return $errors;
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('display_quantities', 'bool')
            ->setAllowedTypes('allow_add_variant_to_cart_from_listing', 'bool')
            ->setAllowedTypes('attribute_anchor_separator', 'string')
            ->setAllowedTypes('display_discount_price', 'bool');

        return $resolver;
    }
}
