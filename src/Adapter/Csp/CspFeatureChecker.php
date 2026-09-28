<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

/**
 * Decides whether CSP is active, shared by the storefront header builder and the report collector
 * so both no-op under exactly the same conditions. Lives in services the hand-built front-office
 * container can resolve.
 */
final class CspFeatureChecker
{
    public function __construct(
        private readonly FeatureFlagStateCheckerInterface $featureFlagChecker,
        private readonly ShopConfigurationInterface $configuration,
    ) {
    }

    public function isFeatureFlagEnabled(): bool
    {
        return $this->featureFlagChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CSP);
    }

    public function isEnabledForShop(int $shopId): bool
    {
        return $this->isFeatureFlagEnabled()
            && (bool) $this->configuration->get('PS_CSP_ENABLED', false, ShopConstraint::shop($shopId));
    }
}
