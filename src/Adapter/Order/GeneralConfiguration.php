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

namespace PrestaShop\PrestaShop\Adapter\Order;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;

/**
 * General Settings configuration available in ShopParameters > Order Preferences.
 */
class GeneralConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'enable_final_summary' => $this->configuration->getBoolean('PS_FINAL_SUMMARY_ENABLED'),
            'enable_guest_checkout' => $this->configuration->getBoolean('PS_GUEST_CHECKOUT_ENABLED'),
            'disable_reordering_option' => $this->configuration->getBoolean('PS_DISALLOW_HISTORY_REORDERING'),
            'purchase_minimum_value' => $this->configuration->get('PS_PURCHASE_MINIMUM'),
            'recalculate_shipping_cost' => $this->configuration->getBoolean('PS_ORDER_RECALCULATE_SHIPPING'),
            'allow_multishipping' => $this->configuration->getBoolean('PS_ALLOW_MULTISHIPPING'),
            'allow_delayed_shipping' => $this->configuration->getBoolean('PS_SHIP_WHEN_AVAILABLE'),
            'enable_tos' => $this->configuration->getBoolean('PS_CONDITIONS'),
            'tos_cms_id' => $this->configuration->get('PS_CONDITIONS_CMS_ID'),
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
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['enable_final_summary'],
            $configuration['enable_guest_checkout'],
            $configuration['disable_reordering_option'],
            $configuration['purchase_minimum_value'],
            $configuration['recalculate_shipping_cost'],
            $configuration['allow_multishipping'],
            $configuration['allow_delayed_shipping'],
            $configuration['enable_tos'],
            $configuration['tos_cms_id']
        );
    }
}
