<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Csp\CspPolicy;
use PrestaShop\PrestaShop\Core\Csp\CspPolicyHookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
use Psr\Log\LoggerInterface;

/**
 * Builds the storefront CSP policy for a shop by additively merging the base policy, curated rules,
 * theme contributions (global_settings.csp) and module hook contributions.
 * Invalid theme entries are logged and skipped.
 */
final class CspPolicyProvider
{
    public function __construct(
        private readonly CspRuleRepository $ruleRepository,
        private readonly CspPolicyHookDispatcherInterface $hookDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string, list<string>> $themeContributions directive => sources from the active theme's
     * global_settings.csp
     */
    public function getPolicy(int $shopId, array $themeContributions = []): CspPolicy
    {
        $policy = new CspPolicy();

        $this->addBasePolicy($policy);
        $this->addCuratedRules($policy, $shopId);
        $this->addThemeContributions($policy, $themeContributions);
        $this->hookDispatcher->dispatch($policy);

        return $policy;
    }

    private function addBasePolicy(CspPolicy $policy): void
    {
        // Tight collection baseline; only ubiquitous, low-risk data: images and fonts are pre-allowed.
        $policy->addSource('default-src', "'self'");

        // Hardening directives that don't fall back to default-src, so they must be set explicitly.
        $policy->addSource('base-uri', "'self'");
        $policy->addSource('frame-ancestors', "'self'");
        $policy->addSource('form-action', "'self'");
        $policy->addSource('object-src', "'none'");

        $policy->addSource('img-src', "'self'");
        $policy->addSource('img-src', 'data:');
        $policy->addSource('font-src', "'self'");
        $policy->addSource('font-src', 'data:');

        // Seed 'self' on every default-src-fallback directive,
        // so that curating one source on it doesn't drop same-origin assets under enforcement.
        $policy->addSource('script-src', "'self'");
        $policy->addSource('style-src', "'self'");
        $policy->addSource('child-src', "'self'");
        $policy->addSource('connect-src', "'self'");
        $policy->addSource('frame-src', "'self'");
        $policy->addSource('manifest-src', "'self'");
        $policy->addSource('media-src', "'self'");
        $policy->addSource('worker-src', "'self'");
    }

    private function addCuratedRules(CspPolicy $policy, int $shopId): void
    {
        foreach ($this->ruleRepository->getRulesByShop($shopId) as $rule) {
            $policy->addSource($rule['directive'], $rule['source']);
        }
    }

    /**
     * @param array<string, list<string>> $themeContributions
     */
    private function addThemeContributions(CspPolicy $policy, array $themeContributions): void
    {
        foreach ($themeContributions as $directive => $sources) {
            if (!is_string($directive) || !is_array($sources)) {
                continue;
            }

            if (null === CspDirective::tryFrom($directive)) {
                $this->logger->warning(sprintf('Ignoring theme CSP contribution for unknown directive "%s".', $directive));

                continue;
            }

            foreach ($sources as $source) {
                if (!is_string($source)) {
                    continue;
                }

                // addSource returns false on an invalid source, so we can warn without validating twice.
                if (!$policy->addSource($directive, $source)) {
                    $this->logger->warning(sprintf('Ignoring invalid theme CSP source "%s" for directive "%s".', $source, $directive));
                }
            }
        }
    }
}
