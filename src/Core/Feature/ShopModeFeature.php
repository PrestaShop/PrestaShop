<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Feature;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Feature\Enum\ShopModeEnum;
use Symfony\Component\HttpFoundation\Exception\UnexpectedValueException;

class ShopModeFeature
{
    public const CONFIGURATION_NAME = 'PS_SHOP_MODE';

    public const DEFAULT_SHOP_MODE = ShopModeEnum::SHOP_MODE_B2C_ONLY;

    public function __construct(
        protected readonly Configuration $configuration
    ) {
    }

    public function getCurrentShopMode(): ShopModeEnum
    {
        try {
            return $this->configuration->getEnum(self::CONFIGURATION_NAME, ShopModeEnum::class, self::DEFAULT_SHOP_MODE);
        } catch (UnexpectedValueException) {
            return ShopModeEnum::SHOP_MODE_B2C_ONLY;
        }
    }

    public function isB2BShopModeEnable(): bool
    {
        return match ($this->getCurrentShopMode()) {
            ShopModeEnum::SHOP_MODE_B2B_ONLY, ShopModeEnum::SHOP_MODE_B2B_AND_B2C => true,
            default => false,
        };
    }

    public function isB2CShopModeEnable(): bool
    {
        return match ($this->getCurrentShopMode()) {
            ShopModeEnum::SHOP_MODE_B2C_ONLY, ShopModeEnum::SHOP_MODE_B2B_AND_B2C => true,
            default => false,
        };
    }

    public function update(ShopModeEnum $shopModeEnum): void
    {
        $this->configuration->set(self::CONFIGURATION_NAME, $shopModeEnum->value);
    }
}
