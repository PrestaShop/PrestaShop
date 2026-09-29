<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Csp\CspPolicy;

/** Builds the CSP response headers for a shop (Context-free), or an empty map when CSP is off for the shop. */
final class CspHeaderBuilder
{
    public function __construct(
        private readonly CspFeatureChecker $featureChecker,
        private readonly CspPolicyProvider $policyProvider,
    ) {
    }

    /**
     * @param array<string, list<string>> $themeContributions the active theme's global_settings.csp
     *
     * @return array<string, string> header name => value (empty when CSP is disabled for the shop)
     */
    public function build(int $shopId, string $reportUri, array $themeContributions = []): array
    {
        if (!$this->featureChecker->isEnabledForShop($shopId)) {
            return [];
        }

        // Report-only reports without blocking; enforcement blocks. Both still send reports.
        $headerName = $this->featureChecker->isReportOnlyForShop($shopId)
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';

        // The URI is built internally from the shop's link, but strip control characters, whitespace,
        // quotes and the header/directive delimiters anyway so it can never corrupt a header line.
        $reportUri = (string) preg_replace('/[\x00-\x20\x7F";,]/', '', $reportUri);

        return [
            // Reporting API endpoint group referenced by "report-to" below.
            'Reporting-Endpoints' => sprintf('csp-endpoint="%s"', $reportUri),
            $headerName => $this->renderPolicy($this->policyProvider->getPolicy($shopId, $themeContributions), $reportUri),
        ];
    }

    private function renderPolicy(CspPolicy $policy, string $reportUri): string
    {
        $parts = [];
        // CspPolicy never stores a directive without at least one source.
        foreach ($policy->getDirectives() as $directive => $sources) {
            $parts[] = $directive . ' ' . implode(' ', $sources);
        }

        // Dual emission: report-to for modern browsers, report-uri for Firefox/Safari.
        $parts[] = 'report-uri ' . $reportUri;
        $parts[] = 'report-to csp-endpoint';

        return implode('; ', $parts);
    }
}
