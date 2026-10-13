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
 * Builds the static security response headers (everything except CSP, which is curated separately).
 * The storefront and back office are configured separately: the storefront reads the per-shop PS_SEC_*
 * keys (all off by default), the back office the global PS_SEC_ADMIN_* keys (secure defaults). HSTS is a
 * per-host directive, so it is configured once on the storefront (PS_SEC_HSTS*) and applied to both.
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

    /**
     * Back-office secure defaults, keyed by the PS_SEC_ADMIN_* suffix. Applied in code when a key has
     * never been set, so the back office is hardened out of the box and stays hardened even if the seed
     * is missing (fail-secure); a merchant value (including an explicit "off") overrides them.
     */
    public const ADMIN_DEFAULTS = [
        'NOSNIFF' => '1',
        'FRAME_OPTIONS' => 'SAMEORIGIN',
        'REFERRER_POLICY' => 'strict-origin',
        'PERMISSIONS_POLICY' => 'camera=(), microphone=(), geolocation=()',
    ];

    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
        private readonly FeatureFlagStateCheckerInterface $featureFlagChecker,
    ) {
    }

    /**
     * @param ShopConstraint|null $shopConstraint scope to read the settings for; null reads the current
     *                                            context (the shop being served on the storefront)
     * @param bool $isAdmin whether these headers are for the back office (reads the PS_SEC_ADMIN_* keys)
     *
     * @return array<string, string> header name => value (empty when every header is off)
     */
    public function getHeaders(bool $isSecure, ?ShopConstraint $shopConstraint = null, bool $isAdmin = false): array
    {
        // The whole Security page (CSP + these headers) is opt-in behind the beta feature flag, so a
        // merchant never gets behaviour-changing headers on upgrade without enabling the feature.
        if (!$this->featureFlagChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CSP)) {
            return [];
        }

        // The back office reads its own global keys and falls back to the secure defaults when a key was
        // never set (fail-secure); the storefront reads its per-shop keys and falls back to off.
        $prefix = $isAdmin ? 'PS_SEC_ADMIN_' : 'PS_SEC_';
        $defaults = $isAdmin ? self::ADMIN_DEFAULTS : [];
        $headers = [];

        if ((bool) $this->configuration->get($prefix . 'NOSNIFF', $defaults['NOSNIFF'] ?? false, $shopConstraint)) {
            $headers['X-Content-Type-Options'] = 'nosniff';
        }

        $frameOptions = (string) $this->configuration->get($prefix . 'FRAME_OPTIONS', $defaults['FRAME_OPTIONS'] ?? '', $shopConstraint);
        if (in_array($frameOptions, self::FRAME_OPTIONS, true)) {
            $headers['X-Frame-Options'] = $frameOptions;
        }

        $referrerPolicy = (string) $this->configuration->get($prefix . 'REFERRER_POLICY', $defaults['REFERRER_POLICY'] ?? '', $shopConstraint);
        if (in_array($referrerPolicy, self::REFERRER_POLICIES, true)) {
            $headers['Referrer-Policy'] = $referrerPolicy;
        }

        // HSTS is per-host, so it is shared across surfaces (configured on the storefront). It is only
        // meaningful over HTTPS; sending it on plain HTTP is ignored by browsers and would let a merchant
        // think they are protected when they are not.
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

        $permissionsPolicy = trim((string) $this->configuration->get($prefix . 'PERMISSIONS_POLICY', $defaults['PERMISSIONS_POLICY'] ?? '', $shopConstraint));
        if ($permissionsPolicy !== '') {
            // Strip CR/LF so a stored value can never inject another header.
            $headers['Permissions-Policy'] = (string) preg_replace('/[\r\n]+/', '', $permissionsPolicy);
        }

        return $headers;
    }
}
