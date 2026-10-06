<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

/** Decides whether CSP is active for a surface, shared by the header builders and the report collector so they no-op under the same conditions. */
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

    public function isEnabledForContext(CspContext $context, int $shopId): bool
    {
        // Lockout safety hatch: defining _PS_CSP_ADMIN_DISABLE_ (e.g. in config/defines_custom.inc.php)
        // turns off all back-office CSP without DB or admin access, to recover a shop that enforced a
        // broken admin policy and locked itself out.
        if (CspContext::ADMIN === $context && defined('_PS_CSP_ADMIN_DISABLE_') && _PS_CSP_ADMIN_DISABLE_) {
            return false;
        }

        return $this->isFeatureFlagEnabled()
            && (bool) $this->configuration->get($this->enabledKey($context), false, $this->scope($context, $shopId));
    }

    /** Whether the surface only reports violations; defaults to report-only so it never blocks without an explicit opt-in. */
    public function isReportOnlyForContext(CspContext $context, int $shopId): bool
    {
        return (bool) $this->configuration->get($this->reportOnlyKey($context), true, $this->scope($context, $shopId));
    }

    /**
     * The external endpoint reports are routed to instead of the built-in collector, or '' to use the
     * collector. Only a valid absolute http(s) URL overrides it; anything else falls back to the collector.
     */
    public function reportTargetForContext(CspContext $context, int $shopId): string
    {
        $value = trim((string) $this->configuration->get($this->reportUriKey($context), '', $this->scope($context, $shopId)));

        if ('' === $value
            || false === filter_var($value, FILTER_VALIDATE_URL)
            || !in_array(parse_url($value, PHP_URL_SCHEME), ['http', 'https'], true)
        ) {
            return '';
        }

        return $value;
    }

    public function isEnabledForShop(int $shopId): bool
    {
        return $this->isEnabledForContext(CspContext::FRONT, $shopId);
    }

    public function isReportOnlyForShop(int $shopId): bool
    {
        return $this->isReportOnlyForContext(CspContext::FRONT, $shopId);
    }

    private function enabledKey(CspContext $context): string
    {
        return $context->isPerShop() ? 'PS_CSP_ENABLED' : 'PS_CSP_ADMIN_ENABLED';
    }

    private function reportOnlyKey(CspContext $context): string
    {
        return $context->isPerShop() ? 'PS_CSP_REPORT_ONLY' : 'PS_CSP_ADMIN_REPORT_ONLY';
    }

    private function reportUriKey(CspContext $context): string
    {
        return $context->isPerShop() ? 'PS_CSP_REPORT_URI' : 'PS_CSP_ADMIN_REPORT_URI';
    }

    /** The storefront reads per-shop; the back office is global, so it reads the all-shops value. */
    private function scope(CspContext $context, int $shopId): ShopConstraint
    {
        return $context->isPerShop() ? ShopConstraint::shop($shopId) : ShopConstraint::allShops();
    }
}
