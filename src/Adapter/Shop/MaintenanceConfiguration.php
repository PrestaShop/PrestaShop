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

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * This class loads and saves data configuration for the Maintenance page.
 */
class MaintenanceConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var Context
     */
    private $shopContext;

    public function __construct(Configuration $configuration, Context $shopContext)
    {
        $this->configuration = $configuration;
        $this->shopContext = $shopContext;
    }

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
        if (!$this->shopContext->isAllShopContext()) {
            $configuration = $this->removeDisabledFields($configuration);
            $contextShopGroup = $this->shopContext->getContextShopGroup();
            $contextShopId = $this->shopContext->getContextShopID();
            $contextShopId = (int) $contextShopId > 0 ? $contextShopId : null;

            $shopConstraint = new ShopConstraint(
                $contextShopId,
                $contextShopGroup->id
            );
        }

        if (isset($configuration['enable_shop'])) {
            $this->configuration->set('PS_SHOP_ENABLE', $configuration['enable_shop'], $shopConstraint);
        }
        if (isset($configuration['maintenance_ip'])) {
            $this->configuration->set('PS_MAINTENANCE_IP', $configuration['maintenance_ip'], $shopConstraint);
        }
        if (isset($configuration['maintenance_text'])) {
            $this->configuration->set('PS_MAINTENANCE_TEXT', $configuration['maintenance_text'], $shopConstraint, ['html' => true]);
        }

        return [];
    }

    /**
     * @param array $configuration
     *
     * @return array
     */
    private function removeDisabledFields(array $configuration): array
    {
        if ($this->shopContext->isAllShopContext()) {
            return $configuration;
        }

        foreach ($configuration as $key => $value) {
            if (substr($key, 0, 11) !== 'multistore_' && $configuration['multistore_' . $key] !== true) {
                unset($configuration[$key]);
            }
        }

        return $configuration;
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
