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

declare(strict_types=1);

namespace PrestaShopBundle\Service\Multistore;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Shop;

class CustomizedConfigurationChecker
{
    /**
     * @var ShopConfigurationInterface
     */
    private $configuration;

    public function __construct(ShopConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Tests if a configuration value is overriden for a given shop, not only on the shop itself
     * but also on parent shop group: when a shop inherits an overridden configuration value from his shop group
     * the value is considered to be customized for this shop
     *
     * @param string $configurationKey
     * @param Shop $shop
     * @param bool $isGroupShopContext
     *
     * @return bool
     */
    public function isConfigurationCustomizedForThisShop(string $configurationKey, Shop $shop, bool $isGroupShopContext): bool
    {
        // we don't check group shop customization if we are already in group shop context
        if (!$isGroupShopContext) {
            // check if given configuration is overridden for the parent group shop
            // isStrict must be true, otherwise the method will also check for configuration settings in "all shop" context
            $shopGroupConstraint = ShopConstraint::shopGroup($shop->getShopGroup()->getId(), true);

            if ($this->configuration->has($configurationKey, $shopGroupConstraint)) {
                return true;
            }
        }

        // check if given configuration is overridden for the shop
        $shopConstraint = ShopConstraint::shop($shop->getId(), true);

        return $this->configuration->has($configurationKey, $shopConstraint);
    }
}
