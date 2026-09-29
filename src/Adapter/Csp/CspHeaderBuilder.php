<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Csp\CspPolicy;

/**
 * Builds the CSP response headers for a shop. Context-free: the caller passes the shop id and the
 * absolute report endpoint, so the same builder serves the legacy front controller and the
 * FrontKernel response subscriber. Returns an empty map when CSP is off for the shop, so a caller
 * can send nothing without special-casing.
 */
final class CspHeaderBuilder
{
    public function __construct(
        private readonly CspFeatureChecker $featureChecker,
        private readonly CspPolicyProvider $policyProvider,
    ) {
    }

    /**
     * @param array<string, list<string>> $themeContributions the active theme's global_settings.csp, passed in by
     *                                                        the caller so the builder stays Context-free
     *
     * @return array<string, string> header name => value (empty when CSP is disabled for the shop)
     */
    public function build(int $shopId, string $reportUri, array $themeContributions = []): array
    {
        if (!$this->featureChecker->isEnabledForShop($shopId)) {
            return [];
        }

        return [
            // Reporting API endpoint group referenced by "report-to" below.
            'Reporting-Endpoints' => sprintf('csp-endpoint="%s"', $reportUri),
            // Always report-only in this phase; enforcement (Content-Security-Policy) arrives in phase C.
            'Content-Security-Policy-Report-Only' => $this->renderPolicy($this->policyProvider->getPolicy($shopId, $themeContributions), $reportUri),
        ];
    }

    private function renderPolicy(CspPolicy $policy, string $reportUri): string
    {
        $parts = [];
        foreach ($policy->getDirectives() as $directive => $sources) {
            $parts[] = $sources === [] ? $directive : $directive . ' ' . implode(' ', $sources);
        }

        // Dual emission: report-to for modern browsers, report-uri for Firefox/Safari.
        $parts[] = 'report-uri ' . $reportUri;
        $parts[] = 'report-to csp-endpoint';

        return implode('; ', $parts);
    }
}
