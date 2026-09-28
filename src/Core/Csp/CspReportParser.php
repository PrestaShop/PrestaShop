<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Csp;

/**
 * Parses a browser CSP report body into a flat list of violations, in both the legacy and Reporting-API wire formats.
 */
final class CspReportParser
{
    /** Caps violations per request so a single POST to the public collector can't fan out into unbounded writes. */
    public const MAX_REPORTS = 50;

    /**
     * @return list<array{directive: string, blockedUri: string, documentUri: ?string}>
     */
    public static function parse(string $contentType, string $body): array
    {
        $body = trim($body);
        if ($body === '') {
            return [];
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            return [];
        }

        if (str_contains(strtolower($contentType), 'application/reports+json')) {
            return self::parseReportingApi($data);
        }

        return self::parseLegacyReport($data);
    }

    /**
     * @param array<mixed> $data
     *
     * @return list<array{directive: string, blockedUri: string, documentUri: ?string}>
     */
    private static function parseReportingApi(array $data): array
    {
        $violations = [];

        foreach ($data as $entry) {
            // Compared case-insensitively to stay lenient with untrusted browser/proxy input.
            if (!is_array($entry) || strtolower((string) ($entry['type'] ?? '')) !== 'csp-violation') {
                continue;
            }

            $reportBody = $entry['body'] ?? null;
            if (!is_array($reportBody)) {
                continue;
            }

            $violation = self::buildViolation(
                $reportBody['effectiveDirective'] ?? $reportBody['effective-directive'] ?? null,
                $reportBody['blockedURL'] ?? $reportBody['blocked-uri'] ?? null,
                $reportBody['documentURL'] ?? $reportBody['document-uri'] ?? null,
            );

            if (null !== $violation) {
                $violations[] = $violation;
            }

            if (count($violations) >= self::MAX_REPORTS) {
                break;
            }
        }

        return $violations;
    }

    /**
     * @param array<mixed> $data
     *
     * @return list<array{directive: string, blockedUri: string, documentUri: ?string}>
     */
    private static function parseLegacyReport(array $data): array
    {
        $report = $data['csp-report'] ?? null;
        if (!is_array($report)) {
            return [];
        }

        $violation = self::buildViolation(
            $report['effective-directive'] ?? $report['violated-directive'] ?? null,
            $report['blocked-uri'] ?? null,
            $report['document-uri'] ?? null,
        );

        return null === $violation ? [] : [$violation];
    }

    /**
     * @return array{directive: string, blockedUri: string, documentUri: ?string}|null
     */
    private static function buildViolation(mixed $directive, mixed $blockedUri, mixed $documentUri): ?array
    {
        if (!is_string($directive) || !is_string($blockedUri) || $directive === '' || $blockedUri === '') {
            return null;
        }

        return [
            'directive' => $directive,
            'blockedUri' => $blockedUri,
            'documentUri' => is_string($documentUri) && $documentUri !== '' ? $documentUri : null,
        ];
    }
}
