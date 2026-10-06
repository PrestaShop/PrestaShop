<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Loads and saves the Content Security Policy settings block
 * (Advanced parameters > Security > Content Security Policy).
 */
final class CspConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['enabled', 'report_only', 'retention_days', 'report_uri'];

    public function __construct(
        Configuration $configuration,
        Context $shopContext,
        FeatureInterface $multistoreFeature,
        private readonly CspRuleRepository $cspRuleRepository,
        private readonly TranslatorInterface $translator,
    ) {
        parent::__construct($configuration, $shopContext, $multistoreFeature);
    }

    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'enabled' => (bool) $this->configuration->get('PS_CSP_ENABLED', false, $shopConstraint),
            'report_only' => (bool) $this->configuration->get('PS_CSP_REPORT_ONLY', true, $shopConstraint),
            'retention_days' => (int) $this->configuration->get('PS_CSP_RETENTION_DAYS', 0, $shopConstraint),
            'report_uri' => (string) $this->configuration->get('PS_CSP_REPORT_URI', '', $shopConstraint),
        ];
    }

    public function updateConfiguration(array $configuration)
    {
        // validateConfiguration() throws on invalid input, so this branch is effectively unreachable (kept for parity).
        if (!$this->validateConfiguration($configuration)) {
            return [];
        }

        $shopConstraint = $this->getShopConstraint();

        // Block the report-only -> enforcing switch until the shop has a baseline;
        // enforcing an empty policy would break the storefront.
        if ($this->isTurningOnEnforcement($configuration)
            && !$this->isAlreadyEnforcing($shopConstraint)
            && !$this->hasBaseline($shopConstraint)
        ) {
            return [
                $this->translator->trans(
                    'Enforce the Content Security Policy only after building an allow-list. Either this shop has not added an allowed source yet, or you are editing all shops at once (enforce each shop from its own page after curating it). Keep "Report-only mode" on, review the collected reports, and allow the sources your storefront needs before enforcing.',
                    [],
                    'Admin.Advparameters.Notification'
                ),
            ];
        }

        if (array_key_exists('report_uri', $configuration)) {
            $configuration['report_uri'] = trim((string) $configuration['report_uri']);
        }

        $this->updateConfigurationValue('PS_CSP_ENABLED', 'enabled', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_CSP_REPORT_ONLY', 'report_only', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_CSP_RETENTION_DAYS', 'retention_days', $configuration, $shopConstraint);
        $this->updateConfigurationValue('PS_CSP_REPORT_URI', 'report_uri', $configuration, $shopConstraint);

        return [];
    }

    /**
     * @param array<string, mixed> $configuration
     */
    private function isTurningOnEnforcement(array $configuration): bool
    {
        return array_key_exists('enabled', $configuration) && (bool) $configuration['enabled']
            && array_key_exists('report_only', $configuration) && !(bool) $configuration['report_only'];
    }

    private function isAlreadyEnforcing(?ShopConstraint $shopConstraint): bool
    {
        if (null === $shopConstraint) {
            return false;
        }

        // Read this scope's own setting strictly: a child shop that only inherits an enforcing parent
        // is not itself "already enforcing", so it must still pass the baseline guard before it enforces.
        $strictConstraint = $shopConstraint->clone(true);

        return (bool) $this->configuration->get('PS_CSP_ENABLED', false, $strictConstraint)
            && !(bool) $this->configuration->get('PS_CSP_REPORT_ONLY', true, $strictConstraint);
    }

    private function hasBaseline(?ShopConstraint $shopConstraint): bool
    {
        // An all-shop/group scope has no single shop, so report no baseline and refuse the blanket enforce transition.
        $shopId = $shopConstraint?->getShopId()?->getValue();
        if (null === $shopId) {
            return false;
        }

        // A safe baseline is a curated allow-list. Collected (but unreviewed) violations do not count:
        // enforcing then would block every reported source that was never allowed.
        return $this->cspRuleRepository->existsByShop(CspContext::FRONT, $shopId);
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('enabled', 'bool')
            ->setAllowedTypes('report_only', 'bool')
            ->setAllowedTypes('retention_days', 'int')
            ->setAllowedTypes('report_uri', 'string')
            ->setNormalizer('retention_days', static fn ($resolver, int $value): int => max(0, $value));
    }
}
