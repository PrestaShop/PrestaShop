<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\SecurityHeader;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Loads and saves the back-office static security-header settings. The back office is a single global
 * surface, so every value is read and written at the all-shops scope (the PS_SEC_ADMIN_* keys), never
 * the shop selected in the back office. These ship with secure defaults (seeded at install/upgrade);
 * HSTS is not here because it is per-host and shared with the storefront.
 */
final class AdminSecurityHeadersConfiguration implements DataConfigurationInterface
{
    private const CONFIGURATION_FIELDS = ['nosniff', 'frame_options', 'referrer_policy', 'permissions_policy'];

    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
    ) {
    }

    public function getConfiguration(): array
    {
        $scope = ShopConstraint::allShops();
        // Fall back to the secure defaults when a key was never set, so the form matches what the header
        // actually sends (the back office is hardened out of the box, seeded or not).
        $defaults = SecurityHeadersProvider::ADMIN_DEFAULTS;

        return [
            'nosniff' => (bool) $this->configuration->get('PS_SEC_ADMIN_NOSNIFF', $defaults['NOSNIFF'], $scope),
            'frame_options' => (string) $this->configuration->get('PS_SEC_ADMIN_FRAME_OPTIONS', $defaults['FRAME_OPTIONS'], $scope),
            'referrer_policy' => (string) $this->configuration->get('PS_SEC_ADMIN_REFERRER_POLICY', $defaults['REFERRER_POLICY'], $scope),
            'permissions_policy' => (string) $this->configuration->get('PS_SEC_ADMIN_PERMISSIONS_POLICY', $defaults['PERMISSIONS_POLICY'], $scope),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        if (!$this->validateConfiguration($configuration)) {
            return [];
        }

        // Strip CR/LF so a stored value can never inject another header downstream.
        $permissionsPolicy = trim((string) preg_replace('/[\r\n]+/', '', (string) ($configuration['permissions_policy'] ?? '')));

        $scope = ShopConstraint::allShops();
        $this->configuration->set('PS_SEC_ADMIN_NOSNIFF', !empty($configuration['nosniff']) ? '1' : '0', $scope);
        $this->configuration->set('PS_SEC_ADMIN_FRAME_OPTIONS', (string) ($configuration['frame_options'] ?? ''), $scope);
        $this->configuration->set('PS_SEC_ADMIN_REFERRER_POLICY', (string) ($configuration['referrer_policy'] ?? ''), $scope);
        $this->configuration->set('PS_SEC_ADMIN_PERMISSIONS_POLICY', $permissionsPolicy, $scope);

        return [];
    }

    public function validateConfiguration(array $configuration): bool
    {
        (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('nosniff', 'bool')
            ->setAllowedTypes('frame_options', 'string')
            ->setAllowedTypes('referrer_policy', 'string')
            ->setAllowedTypes('permissions_policy', 'string')
            ->resolve($configuration);

        return true;
    }
}
