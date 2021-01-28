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

namespace PrestaShop\PrestaShop\Adapter\Shop;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;

/**
 * This class loads and saves data configuration for the Maintenance page.
 */
class MaintenanceConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'enable_shop' => $this->configuration->getBoolean('PS_SHOP_ENABLE'),
            'maintenance_ip' => $this->configuration->get('PS_MAINTENANCE_IP'),
            'maintenance_text' => $this->configuration->get('PS_MAINTENANCE_TEXT'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $shopConstraint = null;
        $configurationInputValues = $this->getConfigurationInputValues($configuration);
        $shopConstraint = $this->getShopConstraint();

        $this->updateConfigurationValue('PS_SHOP_ENABLE', 'enable_shop', $configurationInputValues, $shopConstraint);
        $this->updateConfigurationValue('PS_MAINTENANCE_IP', 'maintenance_ip', $configurationInputValues, $shopConstraint);
        $this->updateConfigurationValue('PS_MAINTENANCE_TEXT', 'maintenance_text', $configurationInputValues, $shopConstraint, ['html' => true]);

        return [];
    }

    /**
     * @param array $configuration
     *
     * @return bool
     */
    public function validateConfiguration(array $configuration): bool
    {
        // TODO: Implement validateConfiguration() method.
        return true;
    }
}
