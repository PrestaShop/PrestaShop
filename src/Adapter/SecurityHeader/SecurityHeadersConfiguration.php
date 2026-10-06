<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SecurityHeader;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads and saves the static security-header settings. These are global (one policy for the whole
 * installation, applied to both the storefront and the back office).
 */
final class SecurityHeadersConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private readonly ConfigurationInterface $configuration,
    ) {
    }

    public function getConfiguration(): array
    {
        return [
            'nosniff' => (bool) $this->configuration->get('PS_SEC_NOSNIFF'),
            'frame_options' => (string) $this->configuration->get('PS_SEC_FRAME_OPTIONS'),
            'referrer_policy' => (string) $this->configuration->get('PS_SEC_REFERRER_POLICY'),
            'hsts' => (bool) $this->configuration->get('PS_SEC_HSTS'),
            'hsts_max_age' => (int) $this->configuration->get('PS_SEC_HSTS_MAX_AGE'),
            'hsts_subdomains' => (bool) $this->configuration->get('PS_SEC_HSTS_SUBDOMAINS'),
            'hsts_preload' => (bool) $this->configuration->get('PS_SEC_HSTS_PRELOAD'),
            'permissions_policy' => (string) $this->configuration->get('PS_SEC_PERMISSIONS_POLICY'),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        if (!$this->validateConfiguration($configuration)) {
            return [];
        }

        $this->configuration->set('PS_SEC_NOSNIFF', $configuration['nosniff'] ? '1' : '0');
        $this->configuration->set('PS_SEC_FRAME_OPTIONS', (string) $configuration['frame_options']);
        $this->configuration->set('PS_SEC_REFERRER_POLICY', (string) $configuration['referrer_policy']);
        $this->configuration->set('PS_SEC_HSTS', $configuration['hsts'] ? '1' : '0');
        $this->configuration->set('PS_SEC_HSTS_MAX_AGE', (string) max(0, (int) $configuration['hsts_max_age']));
        $this->configuration->set('PS_SEC_HSTS_SUBDOMAINS', $configuration['hsts_subdomains'] ? '1' : '0');
        $this->configuration->set('PS_SEC_HSTS_PRELOAD', $configuration['hsts_preload'] ? '1' : '0');
        $this->configuration->set('PS_SEC_PERMISSIONS_POLICY', trim((string) $configuration['permissions_policy']));

        return [];
    }

    public function validateConfiguration(array $configuration): bool
    {
        (new OptionsResolver())
            ->setRequired([
                'nosniff',
                'frame_options',
                'referrer_policy',
                'hsts',
                'hsts_max_age',
                'hsts_subdomains',
                'hsts_preload',
                'permissions_policy',
            ])
            ->setAllowedTypes('nosniff', 'bool')
            ->setAllowedTypes('frame_options', 'string')
            ->setAllowedTypes('referrer_policy', 'string')
            ->setAllowedTypes('hsts', 'bool')
            ->setAllowedTypes('hsts_max_age', 'int')
            ->setAllowedTypes('hsts_subdomains', 'bool')
            ->setAllowedTypes('hsts_preload', 'bool')
            ->setAllowedTypes('permissions_policy', 'string')
            ->resolve($configuration);

        return true;
    }
}
