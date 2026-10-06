<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SecurityHeader;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;

/**
 * Builds the static security response headers (everything except CSP, which is curated separately)
 * from the global settings. Context-free and shared by the storefront and back-office emitters.
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
        private readonly ConfigurationInterface $configuration,
        private readonly FeatureFlagStateCheckerInterface $featureFlagChecker,
    ) {
    }

    /**
     * @return array<string, string> header name => value (empty when every header is off)
     */
    public function getHeaders(bool $isSecure): array
    {
        // The whole Security page (CSP + these headers) is opt-in behind the beta feature flag, so a
        // merchant never gets behaviour-changing headers on upgrade without enabling the feature.
        if (!$this->featureFlagChecker->isEnabled(FeatureFlagSettings::FEATURE_FLAG_CSP)) {
            return [];
        }

        $headers = [];

        if ((bool) $this->configuration->get('PS_SEC_NOSNIFF')) {
            $headers['X-Content-Type-Options'] = 'nosniff';
        }

        $frameOptions = (string) $this->configuration->get('PS_SEC_FRAME_OPTIONS');
        if (in_array($frameOptions, self::FRAME_OPTIONS, true)) {
            $headers['X-Frame-Options'] = $frameOptions;
        }

        $referrerPolicy = (string) $this->configuration->get('PS_SEC_REFERRER_POLICY');
        if (in_array($referrerPolicy, self::REFERRER_POLICIES, true)) {
            $headers['Referrer-Policy'] = $referrerPolicy;
        }

        // HSTS is only meaningful over HTTPS; sending it on plain HTTP is ignored by browsers and would
        // let a merchant think they are protected when they are not.
        if ($isSecure && (bool) $this->configuration->get('PS_SEC_HSTS')) {
            $value = 'max-age=' . max(0, (int) $this->configuration->get('PS_SEC_HSTS_MAX_AGE'));
            if ((bool) $this->configuration->get('PS_SEC_HSTS_SUBDOMAINS')) {
                $value .= '; includeSubDomains';
            }
            if ((bool) $this->configuration->get('PS_SEC_HSTS_PRELOAD')) {
                $value .= '; preload';
            }
            $headers['Strict-Transport-Security'] = $value;
        }

        $permissionsPolicy = trim((string) $this->configuration->get('PS_SEC_PERMISSIONS_POLICY'));
        if ($permissionsPolicy !== '') {
            // Strip CR/LF so a stored value can never inject another header.
            $headers['Permissions-Policy'] = (string) preg_replace('/[\r\n]+/', '', $permissionsPolicy);
        }

        return $headers;
    }
}
