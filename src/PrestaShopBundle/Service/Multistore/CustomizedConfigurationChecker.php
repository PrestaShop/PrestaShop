<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
