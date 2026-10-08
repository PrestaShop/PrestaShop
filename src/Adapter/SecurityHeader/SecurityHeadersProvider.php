<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SecurityHeader;

use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

/**
 * Builds the static security response headers (everything except CSP, which is curated separately)
 * from the per-shop settings. Shared by the storefront and back-office emitters, which pass the shop
 * scope to read: the storefront reads the shop being served (null constraint = current context), the
 * back office reads the all-shops value.
 */
final class SecurityHeadersProvider
{
    /** Values the merchant may choose for Referrer-Policy; anything else is treated as "off". */
    public const REFERRER_POLICIES = [
        'no-referrer',
        'no-referrer-when-downgrade',
        'origin',
        'origin-when-cross-origin',
        'same-origin',
        'strict-origin',
        'strict-origin-when-cross-origin',
        'unsafe-url',
    ];

    /** Values the merchant may choose for X-Frame-Options. */
    public const FRAME_OPTIONS = ['SAMEORIGIN', 'DENY'];

    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
        private readonly FeatureFlagStateCheckerInterface $featureFlagChecker,
    ) {
    }

    /**
     * @param ShopConstraint|null $shopConstraint scope to read the settings for; null reads the current
     *                                            context (the shop being served on the storefront)
     *
     * @return array<string, string> header name => value (empty when every header is off)
     */
    public function getHeaders(bool $isSecure, ?ShopConstraint $shopConstraint = null): array
    {
        // The whole Security page (CSP + these headers) is opt-in behind the beta feature flag, so a
        // merchant never gets behaviour-changing headers on upgrade without enabling the feature.
        if (!$this->featureFlagChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CSP)) {
            return [];
        }

        $headers = [];

        if ((bool) $this->configuration->get('PS_SEC_NOSNIFF', false, $shopConstraint)) {
            $headers['X-Content-Type-Options'] = 'nosniff';
        }

        $frameOptions = (string) $this->configuration->get('PS_SEC_FRAME_OPTIONS', '', $shopConstraint);
        if (in_array($frameOptions, self::FRAME_OPTIONS, true)) {
            $headers['X-Frame-Options'] = $frameOptions;
        }

        $referrerPolicy = (string) $this->configuration->get('PS_SEC_REFERRER_POLICY', '', $shopConstraint);
        if (in_array($referrerPolicy, self::REFERRER_POLICIES, true)) {
            $headers['Referrer-Policy'] = $referrerPolicy;
        }

        // HSTS is only meaningful over HTTPS; sending it on plain HTTP is ignored by browsers and would
        // let a merchant think they are protected when they are not.
        if ($isSecure && (bool) $this->configuration->get('PS_SEC_HSTS', false, $shopConstraint)) {
            $value = 'max-age=' . max(0, (int) $this->configuration->get('PS_SEC_HSTS_MAX_AGE', 0, $shopConstraint));
            if ((bool) $this->configuration->get('PS_SEC_HSTS_SUBDOMAINS', false, $shopConstraint)) {
                $value .= '; includeSubDomains';
            }
            if ((bool) $this->configuration->get('PS_SEC_HSTS_PRELOAD', false, $shopConstraint)) {
                $value .= '; preload';
            }
            $headers['Strict-Transport-Security'] = $value;
        }

        $permissionsPolicy = trim((string) $this->configuration->get('PS_SEC_PERMISSIONS_POLICY', '', $shopConstraint));
        if ($permissionsPolicy !== '') {
            // Strip CR/LF so a stored value can never inject another header.
            $headers['Permissions-Policy'] = (string) preg_replace('/[\r\n]+/', '', $permissionsPolicy);
        }

        return $headers;
    }
}
