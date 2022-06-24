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

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class StockConfiguration is responsible for saving & loading products stock configuration.
 */
class StockConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'allow_ordering_oos',
        'stock_management',
        'in_stock_label',
        'oos_allowed_backorders',
        'oos_denied_backorders',
        'delivery_time',
        'oos_delivery_time',
        'pack_stock_management',
        'oos_show_label_listing_pages',
        'display_last_quantities',
        'display_unavailable_attributes',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'allow_ordering_oos' => (bool) $this->configuration->get('PS_ORDER_OUT_OF_STOCK', false, $shopConstraint),
            'stock_management' => (bool) $this->configuration->get('PS_STOCK_MANAGEMENT', false, $shopConstraint),
            'in_stock_label' => $this->configuration->get('PS_LABEL_IN_STOCK_PRODUCTS', null, $shopConstraint),
            'oos_allowed_backorders' => $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOA', null, $shopConstraint),
            'oos_denied_backorders' => $this->configuration->get('PS_LABEL_OOS_PRODUCTS_BOD', null, $shopConstraint),
            'delivery_time' => $this->configuration->get('PS_LABEL_DELIVERY_TIME_AVAILABLE', null, $shopConstraint),
            'oos_delivery_time' => $this->configuration->get('PS_LABEL_DELIVERY_TIME_OOSBOA', null, $shopConstraint),
            'pack_stock_management' => (int) $this->configuration->get('PS_PACK_STOCK_TYPE', 0, $shopConstraint),
            'oos_show_label_listing_pages' => (bool) $this->configuration->get('PS_SHOW_LABEL_OOS_LISTING_PAGES', false, $shopConstraint),
            'display_last_quantities' => (int) $this->configuration->get('PS_LAST_QTIES', 0, $shopConstraint),
            'display_unavailable_attributes' => (bool) $this->configuration->get('PS_DISP_UNAVAILABLE_ATTR', false, $shopConstraint),
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

            $this->updateConfigurationValue('PS_ORDER_OUT_OF_STOCK', 'allow_ordering_oos', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_STOCK_MANAGEMENT', 'stock_management', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_IN_STOCK_PRODUCTS', 'in_stock_label', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_OOS_PRODUCTS_BOA', 'oos_allowed_backorders', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_OOS_PRODUCTS_BOD', 'oos_denied_backorders', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_DELIVERY_TIME_AVAILABLE', 'delivery_time', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LABEL_DELIVERY_TIME_OOSBOA', 'oos_delivery_time', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_PACK_STOCK_TYPE', 'pack_stock_management', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_SHOW_LABEL_OOS_LISTING_PAGES', 'oos_show_label_listing_pages', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_LAST_QTIES', 'display_last_quantities', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_DISP_UNAVAILABLE_ATTR', 'display_unavailable_attributes', $config, $shopConstraint);
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
            ->setAllowedTypes('allow_ordering_oos', 'bool')
            ->setAllowedTypes('stock_management', 'bool')
            ->setAllowedTypes('in_stock_label', 'string')
            ->setAllowedTypes('oos_allowed_backorders', 'string')
            ->setAllowedTypes('oos_denied_backorders', 'string')
            ->setAllowedTypes('delivery_time', 'string')
            ->setAllowedTypes('oos_delivery_time', 'string')
            ->setAllowedTypes('pack_stock_management', 'int')
            ->setAllowedTypes('oos_show_label_listing_pages', 'bool')
            ->setAllowedTypes('display_last_quantities', 'int')
            ->setAllowedTypes('display_unavailable_attributes', 'bool');

        return $resolver;
    }
}
