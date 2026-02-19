<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\Checkout;

use Exception;
use PrestaShop\PrestaShop\Core\Checkout\OnePageCheckoutAvailabilityCheckerInterface;
use PrestaShop\PrestaShop\Core\Checkout\OnePageCheckoutSettings;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

/**
 * Determines if One Page Checkout can be used for the specified shop based on configuration and feature flag state.
 */
final class OnePageCheckoutAvailabilityChecker implements OnePageCheckoutAvailabilityCheckerInterface
{
    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabledForShop(int $shopId): bool
    {
        try {
            $isConfigurationEnabled = (bool) $this->configuration->get(
                OnePageCheckoutSettings::ONE_PAGE_CHECKOUT_ENABLED,
                false,
                ShopConstraint::shop($shopId)
            );

            if (!$isConfigurationEnabled) {
                return false;
            }

            return $this->featureFlagStateChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_ONE_PAGE_CHECKOUT);
        } catch (Exception) {
            return false;
        }
    }
}
