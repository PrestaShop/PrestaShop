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

namespace PrestaShop\PrestaShop\Adapter\Carrier;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;

/**
 * Class HandlingConfiguration is responsible for saving and loading Handling options configuration.
 */
class HandlingConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'shipping_handling_charges' => $this->configuration->get('PS_SHIPPING_HANDLING'),
            'free_shipping_price' => $this->configuration->get('PS_SHIPPING_FREE_PRICE'),
            'free_shipping_weight' => $this->configuration->get('PS_SHIPPING_FREE_WEIGHT'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_SHIPPING_HANDLING', 'shipping_handling_charges', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_SHIPPING_FREE_PRICE', 'free_shipping_price', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_SHIPPING_FREE_WEIGHT', 'free_shipping_weight', $configuration, $shopConstraint);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['shipping_handling_charges'],
            $configuration['free_shipping_price'],
            $configuration['free_shipping_weight']
        );
    }
}
