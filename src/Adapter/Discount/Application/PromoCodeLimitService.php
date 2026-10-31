<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Discount\Application;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\CartPromoCodeRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountPriority;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

class PromoCodeLimitService
{
    public function __construct(
        private readonly CartPromoCodeRepository $cartPromoCodeRepository,
        private readonly ShopConfigurationInterface $configuration,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker
    ) {
    }

    /**
     * Check whether a coded cart rule can remain or be added to a cart.
     *
     * Shops without the setting retain the historical behavior; new installations
     * seed it to false in configuration.xml.
     */
    public function canApplyPromoCode(int $cartId, int $shopId, int $cartRuleId, bool $alreadyInCart): bool
    {
        if ((bool) $this->configuration->get(
            'PS_CART_RULE_ALLOW_MULTIPLE_CODES',
            true,
            ShopConstraint::shop($shopId)
        )) {
            return true;
        }

        $promoCodes = $this->cartPromoCodeRepository->getPromoCodesForCart($cartId);
        if (!$alreadyInCart) {
            return empty($promoCodes);
        }

        if (empty($promoCodes)) {
            return true;
        }

        if ($this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_DISCOUNT)) {
            $promoCodes = DiscountPriority::sortByPriority($promoCodes);
        }

        return (int) $promoCodes[0]['id'] === $cartRuleId;
    }
}
