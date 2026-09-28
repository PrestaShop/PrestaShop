<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Adapter\Csp\CspFeatureChecker;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\Csp\CspReportParser;

/**
 * Public, unauthenticated endpoint that receives browser CSP violation reports and hands them to
 * the recorder. It holds no logic of its own: read the body, record, answer 204. Kept as a legacy
 * front controller so it works on both the default dispatch and the FrontKernel fallback, and so
 * the report-uri never points inside the admin directory.
 */
class CspReportControllerCore extends FrontController
{
    /**
     * Browsers send small JSON documents; anything larger is not a genuine report and is ignored.
     */
    private const MAX_BODY_SIZE = 65536;

    public function postProcess()
    {
        $this->collectReports();

        // No body, no template: the browser ignores the response, and a 204 keeps the endpoint cheap.
        header('HTTP/1.1 204 No Content', true, 204);
        exit;
    }

    private function collectReports(): void
    {
        if (!isset($_SERVER['REQUEST_METHOD']) || strtoupper((string) $_SERVER['REQUEST_METHOD']) !== 'POST') {
            return;
        }

        $shopId = (int) $this->context->shop->id;

        /** @var CspFeatureChecker $featureChecker */
        $featureChecker = $this->get(CspFeatureChecker::class);
        if (!$featureChecker->isEnabledForShop($shopId)) {
            return;
        }

        // Read at most one byte past the cap so an oversized body is rejected without ever holding
        // the whole payload in memory on this public, unauthenticated write path.
        $body = file_get_contents('php://input', false, null, 0, self::MAX_BODY_SIZE + 1);
        if (!is_string($body) || $body === '' || strlen($body) > self::MAX_BODY_SIZE) {
            return;
        }

        $contentType = isset($_SERVER['CONTENT_TYPE']) ? (string) $_SERVER['CONTENT_TYPE'] : '';
        $reports = CspReportParser::parse($contentType, $body);
        if ($reports === []) {
            return;
        }

        /** @var CspViolationRecorder $recorder */
        $recorder = $this->get(CspViolationRecorder::class);
        foreach ($reports as $report) {
            $recorder->record($shopId, $report['directive'], $report['blockedUri'], $report['documentUri']);
        }
    }
}
