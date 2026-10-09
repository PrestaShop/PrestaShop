<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Csp\CspPolicy;
use PrestaShop\PrestaShop\Core\Csp\CspPolicyHookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
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
    /**
     * First-party sources the back office needs, pre-allowed so a merchant never curates core's own
     * infrastructure. Each host sits on the directive it is fetched on; weakening keywords are never
     * pre-baked, so enabling admin CSP cannot silently weaken script-src.
     *
     * @var array<string, list<string>>
     */
    private const ADMIN_FIRST_PARTY_SOURCES = [
        // Addons marketplace / MBO module assets served from PrestaShop's CDNs.
        'script-src' => ['https://storage.googleapis.com', 'https://assets.prestashop3.com'],
        // Google Fonts "Open Sans" loaded by the legacy Help popup (the modern BO self-hosts its fonts).
        'style-src' => ['https://fonts.googleapis.com'],
        'font-src' => ['https://fonts.gstatic.com'],
        // The distribution API client fetches the open-source project APIs (contributors, devdocs) via XHR.
        'connect-src' => ['https://*.prestashop-project.org'],
        // Addons marketplace / MBO thumbnails, and employee Gravatar avatars.
        'img-src' => ['https://*.prestashop.com', 'https://*.gravatar.com'],
        // Addons marketplace / MBO recommendation iframes.
        'frame-src' => ['https://*.prestashop.com'],
    ];

    public function __construct(
        private readonly CspRuleRepository $ruleRepository,
        private readonly CspPolicyHookDispatcherInterface $hookDispatcher,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param array<string, list<string>> $themeContributions directive => sources from the active theme's
     *                                                        global_settings.csp
     */
    public function getPolicy(CspContext $context, int $shopId, array $themeContributions = []): CspPolicy
    {
        $policy = new CspPolicy();

        $this->addBasePolicy($policy);

        // The back office pulls a handful of PrestaShop-owned resources; pre-allow them so admin CSP is
        // usable without curating core's own first-party domains. The storefront keeps the tight base.
        if (CspContext::ADMIN === $context) {
            $this->addAdminFirstPartySources($policy);
        }

        $this->addCuratedRules($policy, $context, $shopId);

        // Theme contributions and the storefront policy hook only apply to the front office; the back
        // office is a core-owned surface whose policy is the base plus its own curated rules.
        if (CspContext::FRONT === $context) {
            $this->addThemeContributions($policy, $themeContributions);
            $this->hookDispatcher->dispatch($policy);
        }

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

    /**
     * The back-office sources pre-allowed by default, flattened to {directive, source} rows so the admin UI
     * can show the merchant what the back office permits out of the box (these never produce a violation).
     *
     * @return list<array{directive: string, source: string}>
     */
    public static function getAdminFirstPartySources(): array
    {
        $rows = [];
        foreach (self::ADMIN_FIRST_PARTY_SOURCES as $directive => $sources) {
            foreach ($sources as $source) {
                $rows[] = ['directive' => $directive, 'source' => $source];
            }
        }

        return $rows;
    }

    private function addAdminFirstPartySources(CspPolicy $policy): void
    {
        foreach (self::ADMIN_FIRST_PARTY_SOURCES as $directive => $sources) {
            foreach ($sources as $source) {
                $policy->addSource($directive, $source);
            }
        }
    }

    private function addCuratedRules(CspPolicy $policy, CspContext $context, int $shopId): void
    {
        foreach ($this->ruleRepository->getRulesByShop($context, $shopId) as $rule) {
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
