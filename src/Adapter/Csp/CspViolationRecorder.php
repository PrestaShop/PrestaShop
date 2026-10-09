<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Csp\CspReportNormalizer;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspSource;
use PrestaShopBundle\Entity\Repository\CspLogRepository;

/** Single integration point for collecting and clearing CSP violation reports, shared by the front- and back-office. */
final class CspViolationRecorder
{
    public const DEFAULT_ROW_CAP = 1000;

    // The document URI is part of the unique key (one row per source per page), so it is bounded to an
    // index-friendly length; it is already query/fragment-stripped, so the path rarely approaches this.
    private const MAX_URI_LENGTH = 255;

    // The browser caps its code sample at 40 chars; keep a little headroom. Informational only (not keyed).
    private const MAX_SAMPLE_LENGTH = 64;

    public function __construct(
        private readonly CspLogRepository $repository,
        private readonly int $rowCap = self::DEFAULT_ROW_CAP,
    ) {
    }

    /**
     * Records one reported violation; unknown directives and junk/invalid sources are dropped silently
     * (untrusted browser data).
     *
     * At the per-shop row cap the log stops accepting *new* sources: an already-recorded source keeps
     * counting (its hit counter is bumped), but a brand-new one is refused until the log is pruned or
     * cleared. This holds the cap without a COUNT on every insert and, unlike evicting the lowest-hit
     * rows, means a flood of one-off fake sources can never push out the genuine low-hit violations the
     * merchant still has to curate.
     *
     * @return bool whether a new row was inserted
     */
    public function record(
        CspContext $context,
        int $shopId,
        string $rawDirective,
        string $rawSource,
        ?string $documentUri,
        ?string $sample = null,
        ?string $sourceFile = null,
        ?int $lineNumber = null,
    ): bool {
        $directiveName = CspReportNormalizer::normalizeDirective($rawDirective);
        if (null === $directiveName) {
            return false;
        }

        $directive = CspDirective::tryFrom($directiveName);
        if (null === $directive) {
            return false;
        }

        $sourceExpression = CspReportNormalizer::normalizeSource($rawSource);
        if (null === $sourceExpression) {
            return false;
        }

        try {
            $source = new CspSource($sourceExpression);
        } catch (CspConstraintException) {
            return false;
        }

        $documentUri = $this->sanitizeUri($documentUri) ?? '';
        $sample = $this->normalizeSample($sample);
        $sourceFile = $this->sanitizeUri($sourceFile);
        $lineNumber = (null !== $lineNumber && $lineNumber > 0) ? $lineNumber : null;

        // At the cap, keep counting a known source but refuse a new one (see the method docblock).
        if ($this->rowCap > 0 && $this->repository->hasAtLeast($context, $shopId, $this->rowCap)) {
            $this->repository->bumpIfExists($context, $shopId, $directive->value, $source->getValue(), $documentUri, $sample, $sourceFile, $lineNumber);

            return false;
        }

        return $this->repository->upsert($context, $shopId, $directive->value, $source->getValue(), $documentUri, $sample, $sourceFile, $lineNumber);
    }

    public function clear(CspContext $context, int $shopId): void
    {
        $this->repository->deleteByShop($context, $shopId);
    }

    /** Strips the query/fragment and caps the length of a reported URL (document URL or source file). */
    private function sanitizeUri(?string $uri): ?string
    {
        if (null === $uri) {
            return null;
        }

        $uri = trim($uri);
        if ($uri === '') {
            return null;
        }

        // Drop the query/fragment: a reported URL can carry tokens,
        // order keys or emails that must not land in a visible log.
        $parts = parse_url($uri);
        if (is_array($parts) && !empty($parts['host'])) {
            $uri = (isset($parts['scheme']) ? $parts['scheme'] . '://' : '')
                . $parts['host']
                . (isset($parts['port']) ? ':' . $parts['port'] : '')
                . ($parts['path'] ?? '');
        } else {
            // Not a parseable absolute URL; still strip from the query/fragment onward.
            $uri = preg_replace('/[?#].*$/s', '', $uri);
        }

        return mb_substr($uri, 0, self::MAX_URI_LENGTH);
    }

    /** Trims and caps the browser's code sample; empty becomes null (nothing to show). */
    private function normalizeSample(?string $sample): ?string
    {
        if (null === $sample) {
            return null;
        }

        $sample = trim($sample);
        if ($sample === '') {
            return null;
        }

        return mb_substr($sample, 0, self::MAX_SAMPLE_LENGTH);
    }

    public function enforceRowCap(CspContext $context, int $shopId): void
    {
        // Approximate by design: COUNT-then-DELETE isn't transactional, so the table can briefly sit over the cap.
        $count = $this->repository->countByShop($context, $shopId);
        if ($count > $this->rowCap) {
            $this->repository->deleteLeastReportedByShop($context, $shopId, $count - $this->rowCap);
        }
    }
}
