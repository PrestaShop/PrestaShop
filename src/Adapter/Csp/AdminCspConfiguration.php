<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Loads and saves the back-office Content Security Policy settings. Unlike the storefront settings
 * these are global (one back office across the whole installation), so they are stored as plain global
 * configuration values, and the admin allow-list lives under context=admin, shop id 0.
 */
final class AdminCspConfiguration implements DataConfigurationInterface
{
    /** The back office is a single global surface; its rules/log use shop id 0. */
    private const ADMIN_SHOP_ID = 0;

    public function __construct(
        private readonly ConfigurationInterface $configuration,
        private readonly CspRuleRepository $cspRuleRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function getConfiguration(): array
    {
        return [
            'enabled' => (bool) $this->configuration->get('PS_CSP_ADMIN_ENABLED'),
            'report_only' => $this->isReportOnly(),
            'retention_days' => (int) $this->configuration->get('PS_CSP_ADMIN_RETENTION_DAYS'),
            'report_uri' => (string) $this->configuration->get('PS_CSP_ADMIN_REPORT_URI'),
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

        $this->configuration->set('PS_CSP_ADMIN_ENABLED', $configuration['enabled'] ? '1' : '0');
        $this->configuration->set('PS_CSP_ADMIN_REPORT_ONLY', $configuration['report_only'] ? '1' : '0');
        $this->configuration->set('PS_CSP_ADMIN_RETENTION_DAYS', (string) max(0, (int) $configuration['retention_days']));
        $this->configuration->set('PS_CSP_ADMIN_REPORT_URI', trim((string) $configuration['report_uri']));

        return [];
    }

    public function validateConfiguration(array $configuration): bool
    {
        (new OptionsResolver())
            ->setRequired(['enabled', 'report_only', 'retention_days', 'report_uri'])
            ->setAllowedTypes('enabled', 'bool')
            ->setAllowedTypes('report_only', 'bool')
            ->setAllowedTypes('retention_days', 'int')
            ->setAllowedTypes('report_uri', 'string')
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
        return (bool) $this->configuration->get('PS_CSP_ADMIN_ENABLED') && !$this->isReportOnly();
    }

    /** Defaults to report-only so the back office never blocks without an explicit opt-in. */
    private function isReportOnly(): bool
    {
        $value = $this->configuration->get('PS_CSP_ADMIN_REPORT_ONLY');

        return null === $value ? true : (bool) $value;
    }

    private function hasBaseline(): bool
    {
        return $this->cspRuleRepository->existsByShop(CspContext::ADMIN, self::ADMIN_SHOP_ID);
    }
}
