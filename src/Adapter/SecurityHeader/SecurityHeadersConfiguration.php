<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SecurityHeader;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads and saves the static security-header settings
 * (Advanced parameters > Security > Security headers). Per-shop: the storefront applies the shop's
 * own values, and the back office uses the all-shops value.
 */
final class SecurityHeadersConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        'nosniff',
        'frame_options',
        'referrer_policy',
        'hsts',
        'hsts_max_age',
        'hsts_subdomains',
        'hsts_preload',
        'permissions_policy',
    ];

    public function getConfiguration(): array
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'nosniff' => (bool) $this->configuration->get('PS_SEC_NOSNIFF', false, $shopConstraint),
            'frame_options' => (string) $this->configuration->get('PS_SEC_FRAME_OPTIONS', '', $shopConstraint),
            'referrer_policy' => (string) $this->configuration->get('PS_SEC_REFERRER_POLICY', '', $shopConstraint),
            'hsts' => (bool) $this->configuration->get('PS_SEC_HSTS', false, $shopConstraint),
            'hsts_max_age' => (int) $this->configuration->get('PS_SEC_HSTS_MAX_AGE', 0, $shopConstraint),
            'hsts_subdomains' => (bool) $this->configuration->get('PS_SEC_HSTS_SUBDOMAINS', false, $shopConstraint),
            'hsts_preload' => (bool) $this->configuration->get('PS_SEC_HSTS_PRELOAD', false, $shopConstraint),
            'permissions_policy' => (string) $this->configuration->get('PS_SEC_PERMISSIONS_POLICY', '', $shopConstraint),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        // validateConfiguration() throws on invalid input, so this branch is effectively unreachable (kept for parity).
        if (!$this->validateConfiguration($configuration)) {
            return [];
        }

        if (array_key_exists('hsts_max_age', $configuration)) {
            $configuration['hsts_max_age'] = max(0, (int) $configuration['hsts_max_age']);
        }
        if (array_key_exists('permissions_policy', $configuration)) {
            // Strip CR/LF so a stored value can never inject another header downstream.
            $configuration['permissions_policy'] = trim((string) preg_replace('/[\r\n]+/', '', (string) $configuration['permissions_policy']));
        }

        $shopConstraint = $this->getShopConstraint();

        $this->updateConfigurationValue('PS_SEC_NOSNIFF', 'nosniff', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SEC_FRAME_OPTIONS', 'frame_options', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SEC_REFERRER_POLICY', 'referrer_policy', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SEC_HSTS', 'hsts', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SEC_HSTS_MAX_AGE', 'hsts_max_age', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SEC_HSTS_SUBDOMAINS', 'hsts_subdomains', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SEC_HSTS_PRELOAD', 'hsts_preload', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_SEC_PERMISSIONS_POLICY', 'permissions_policy', $configuration, $shopConstraint);

        return [];
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('nosniff', 'bool')
            ->setAllowedTypes('frame_options', 'string')
            ->setAllowedTypes('referrer_policy', 'string')
            ->setAllowedTypes('hsts', 'bool')
            ->setAllowedTypes('hsts_max_age', 'int')
            ->setAllowedTypes('hsts_subdomains', 'bool')
            ->setAllowedTypes('hsts_preload', 'bool')
            ->setAllowedTypes('permissions_policy', 'string')
            ->setNormalizer('hsts_max_age', static fn ($resolver, int $value): int => max(0, $value));
    }
}
