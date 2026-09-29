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

/** Decides whether CSP is active, shared by the header builder and the report collector so both no-op under the same conditions. */
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

    /** Whether the shop only reports violations; defaults to report-only so a shop never blocks without an explicit opt-in. */
    public function isReportOnlyForShop(int $shopId): bool
    {
        return (bool) $this->configuration->get('PS_CSP_REPORT_ONLY', true, ShopConstraint::shop($shopId));
    }
}
