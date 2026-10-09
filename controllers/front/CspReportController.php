<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

use PrestaShop\PrestaShop\Adapter\Csp\CspFeatureChecker;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\Csp\CspReportParser;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;

/** Public, unauthenticated endpoint that receives browser CSP violation reports: read the body, record, answer 204. */
class CspReportControllerCore extends FrontController
{
    // Real reports are a few KB; cap the body before parsing so an oversized payload is rejected cheaply.
    private const MAX_BODY_SIZE = 32768;

    /**
     * The row-cap prune (COUNT + DELETE) runs on a sample of inserting requests, not every one: under a
     * report flood this stops concurrent DELETEs on one shop's log from piling up, while the cap still
     * holds on average (it is approximate by design, and a prune removes all overflow when it runs).
     */
    private const ROW_CAP_PRUNE_SAMPLING = 10;

    /** Keep recording during maintenance, which is often exactly when a merchant tests enforcement. */
    protected function displayMaintenancePage()
    {
    }

    public function postProcess()
    {
        $this->collectReports();

        // No body, no template: the browser ignores the response, and a 204 keeps the endpoint cheap.
        $this->terminateResponse(204);
    }

    /**
     * The current shop's own hosts (every active shop_url domain + domain_ssl), lower-cased. A real report's
     * document-uri is always on one of these; anything else is forged or misdirected.
     *
     * @return array<string, true> host set, keyed for O(1) lookup
     */
    private function shopHosts(): array
    {
        $hosts = [];
        foreach ($this->context->shop->getUrls() as $url) {
            foreach ([$url['domain'] ?? '', $url['domain_ssl'] ?? ''] as $domain) {
                $domain = Tools::strtolower(trim((string) $domain));
                if ('' !== $domain) {
                    $hosts[$domain] = true;
                }
            }
        }

        // Fall back to the configured main domains, so the check never fails closed on a misconfigured shop_url.
        foreach ([Tools::getShopDomain(false, false), Tools::getShopDomainSsl(false, false)] as $domain) {
            $domain = Tools::strtolower(trim((string) $domain));
            if ('' !== $domain) {
                $hosts[$domain] = true;
            }
        }

        return $hosts;
    }

    /**
     * @param array<string, true> $shopHosts
     */
    private function isOnShopHost(?string $documentUri, array $shopHosts): bool
    {
        if (null === $documentUri || '' === $documentUri) {
            return false;
        }

        $host = parse_url($documentUri, PHP_URL_HOST);
        if (!is_string($host) || '' === $host) {
            return false;
        }

        return isset($shopHosts[Tools::strtolower($host)]);
    }

    /** Sends the status and ends the request. Isolated so a test can observe the code without exiting the runner. */
    protected function terminateResponse(int $statusCode): void
    {
        http_response_code($statusCode);
        exit;
    }

    private function collectReports(): void
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper((string) $_SERVER['REQUEST_METHOD']) !== 'POST') {
            return;
        }

        try {
            // The storefront reports per shop; a back-office report tags itself context=admin and is
            // stored globally (shop id 0).
            $context = 'admin' === Tools::getValue('context') ? CspContext::ADMIN : CspContext::FRONT;
            $shopId = $context->isPerShop() ? (int) $this->context->shop->id : 0;

            /** @var CspFeatureChecker $featureChecker */
            $featureChecker = $this->get(CspFeatureChecker::class);
            if (!$featureChecker->isEnabledForContext($context, $shopId)) {
                return;
            }

            // Read one byte past the cap so an oversized body is rejected without holding the whole payload in memory.
            $body = file_get_contents('php://input', false, null, 0, self::MAX_BODY_SIZE + 1);
            if (!is_string($body) || $body === '' || strlen($body) > self::MAX_BODY_SIZE) {
                return;
            }

            $contentType = isset($_SERVER['CONTENT_TYPE']) ? (string) $_SERVER['CONTENT_TYPE'] : '';
            $reports = CspReportParser::parse($contentType, $body);
            if ($reports === []) {
                return;
            }

            // Drop reports whose document-uri is not on a shop host: a real report comes from a page we
            // served, so this sheds forged payloads and junk pages (the body is public and untrusted).
            $shopHosts = $this->shopHosts();

            /** @var CspViolationRecorder $recorder */
            $recorder = $this->get(CspViolationRecorder::class);
            $anyInserted = false;
            foreach ($reports as $report) {
                if (!$this->isOnShopHost($report['documentUri'], $shopHosts)) {
                    continue;
                }

                $anyInserted = $recorder->record(
                    $context,
                    $shopId,
                    $report['directive'],
                    $report['blockedUri'],
                    $report['documentUri'],
                    $report['sample'],
                    $report['sourceFile'],
                    $report['lineNumber'],
                    false
                ) || $anyInserted;
            }

            // Enforce the row cap once per batch, only when a new row was inserted (bumped counters
            // can't exceed the cap), and only on a sample of requests (see ROW_CAP_PRUNE_SAMPLING).
            if ($anyInserted && 1 === random_int(1, self::ROW_CAP_PRUNE_SAMPLING)) {
                $recorder->enforceRowCap($context, $shopId);
            }
        } catch (Throwable $e) {
            // This public endpoint must never 500 on a DB hiccup: skip recording, still answer 204, but log it.
            try {
                PrestaShopLogger::addLog('CSP report not recorded: ' . $e->getMessage(), 2, null, 'CspReport');
            } catch (Throwable) {
                // The DB-backed logger can fail the same way; fall back to the error log.
                error_log('CSP report not recorded: ' . $e->getMessage());
            }
        }
    }
}
