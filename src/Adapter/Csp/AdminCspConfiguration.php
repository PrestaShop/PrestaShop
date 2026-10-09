<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Loads and saves the back-office Content Security Policy settings. The back office is a single global
 * surface (one back office across the whole installation), so every value is read and written at the
 * all-shops scope, never the shop currently selected in the back office; the header reads the same scope,
 * so a multistore install stores and applies one admin policy. The admin allow-list lives under
 * context=admin, shop id 0. The back office has no external reporting endpoint: its reports always go to
 * the built-in collector, which strips the admin URL's query (CSRF token, secret folder) before storing.
 */
final class AdminCspConfiguration implements DataConfigurationInterface
{
    /** The back office is a single global surface; its rules/log use shop id 0. */
    private const ADMIN_SHOP_ID = 0;

    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
        private readonly CspRuleRepository $cspRuleRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getConfiguration(): array
    {
        $scope = ShopConstraint::allShops();

        return [
            'enabled' => (bool) $this->configuration->get('PS_CSP_ADMIN_ENABLED', false, $scope),
            'report_only' => $this->isReportOnly(),
            'retention_days' => (int) $this->configuration->get('PS_CSP_ADMIN_RETENTION_DAYS', 0, $scope),
        ];
    }

    public function updateConfiguration(array $configuration): array
    {
        if (!$this->validateConfiguration($configuration)) {
            return [];
        }

        // Block the report-only -> enforcing switch until the back office has a curated allow-list;
        // enforcing an empty policy would break the admin and could lock the merchant out.
        if ($this->isTurningOnEnforcement($configuration) && !$this->isAlreadyEnforcing() && !$this->hasBaseline()) {
            return [
                $this->translator->trans(
                    'Enforce the back-office Content Security Policy only after building an allow-list. Keep "Report-only mode" on, review the collected reports, and allow the sources the back office needs before enforcing.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
            ];
        }

        $scope = ShopConstraint::allShops();
        $this->configuration->set('PS_CSP_ADMIN_ENABLED', $configuration['enabled'] ? '1' : '0', $scope);
        $this->configuration->set('PS_CSP_ADMIN_REPORT_ONLY', $configuration['report_only'] ? '1' : '0', $scope);
        $this->configuration->set('PS_CSP_ADMIN_RETENTION_DAYS', (string) max(0, (int) $configuration['retention_days']), $scope);

        return [];
    }

    public function validateConfiguration(array $configuration): bool
    {
        (new OptionsResolver())
            ->setRequired(['enabled', 'report_only', 'retention_days'])
            ->setAllowedTypes('enabled', 'bool')
            ->setAllowedTypes('report_only', 'bool')
            ->setAllowedTypes('retention_days', 'int')
            ->resolve($configuration);

        return true;
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function isTurningOnEnforcement(array $configuration): bool
    {
        return (bool) $configuration['enabled'] && !(bool) $configuration['report_only'];
    }

    private function isAlreadyEnforcing(): bool
    {
        return (bool) $this->configuration->get('PS_CSP_ADMIN_ENABLED', false, ShopConstraint::allShops()) && !$this->isReportOnly();
    }

    /** Defaults to report-only so the back office never blocks without an explicit opt-in. */
    private function isReportOnly(): bool
    {
        $value = $this->configuration->get('PS_CSP_ADMIN_REPORT_ONLY', null, ShopConstraint::allShops());

        return null === $value ? true : (bool) $value;
    }

    private function hasBaseline(): bool
    {
        return $this->cspRuleRepository->existsByShop(CspContext::ADMIN, self::ADMIN_SHOP_ID);
    }
}
