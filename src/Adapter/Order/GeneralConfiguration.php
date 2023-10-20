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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Order;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * General Settings configuration available in ShopParameters > Order Preferences.
 */
class GeneralConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'enable_final_summary',
        'enable_guest_checkout',
        'disable_reordering_option',
        'purchase_minimum_value',
        'recalculate_shipping_cost',
        'allow_multishipping',
        'allow_delayed_shipping',
        'enable_tos',
        'tos_cms_id',
        'enable_backorder_status',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enable_final_summary' => (bool) $this->configuration->get('PS_FINAL_SUMMARY_ENABLED', false, $shopConstraint),
            'enable_guest_checkout' => (bool) $this->configuration->get('PS_GUEST_CHECKOUT_ENABLED', false, $shopConstraint),
            'disable_reordering_option' => (bool) $this->configuration->get('PS_DISALLOW_HISTORY_REORDERING', false, $shopConstraint),
            'purchase_minimum_value' => (float) $this->configuration->get('PS_PURCHASE_MINIMUM', 0, $shopConstraint),
            'recalculate_shipping_cost' => (bool) $this->configuration->get('PS_ORDER_RECALCULATE_SHIPPING', false, $shopConstraint),
            'allow_multishipping' => (bool) $this->configuration->get('PS_ALLOW_MULTISHIPPING', false, $shopConstraint),
            'allow_delayed_shipping' => (bool) $this->configuration->get('PS_SHIP_WHEN_AVAILABLE', false, $shopConstraint),
            'enable_tos' => (bool) $this->configuration->get('PS_CONDITIONS', false, $shopConstraint),
            'tos_cms_id' => (int) $this->configuration->get('PS_CONDITIONS_CMS_ID', 0, $shopConstraint),
            'enable_backorder_status' => (bool) $this->configuration->get('PS_ENABLE_BACKORDER_STATUS', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_FINAL_SUMMARY_ENABLED', 'enable_final_summary', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_GUEST_CHECKOUT_ENABLED', 'enable_guest_checkout', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_DISALLOW_HISTORY_REORDERING', 'disable_reordering_option', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_PURCHASE_MINIMUM', 'purchase_minimum_value', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_ORDER_RECALCULATE_SHIPPING', 'recalculate_shipping_cost', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_ALLOW_MULTISHIPPING', 'allow_multishipping', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_SHIP_WHEN_AVAILABLE', 'allow_delayed_shipping', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CONDITIONS', 'enable_tos', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CONDITIONS_CMS_ID', 'tos_cms_id', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_ENABLE_BACKORDER_STATUS', 'enable_backorder_status', $configuration, $shopConstraint);
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('enable_final_summary', 'bool')
            ->setAllowedTypes('enable_guest_checkout', 'bool')
            ->setAllowedTypes('disable_reordering_option', 'bool')
            ->setAllowedTypes('purchase_minimum_value', 'float')
            ->setAllowedTypes('recalculate_shipping_cost', 'bool')
            ->setAllowedTypes('allow_multishipping', 'bool')
            ->setAllowedTypes('allow_delayed_shipping', 'bool')
            ->setAllowedTypes('enable_tos', 'bool')
            ->setAllowedTypes('tos_cms_id', 'int')
            ->setAllowedTypes('enable_backorder_status', 'bool');

        return $resolver;
    }
}
