<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Csp;

use PrestaShop\PrestaShop\Core\Csp\CspReportNormalizer;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspDirective;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspSource;
use PrestaShopBundle\Entity\Repository\CspLogRepository;

/**
 * Single integration point for collecting and clearing CSP violation reports. Both the
 * front-office collector (plain service call) and the back-office command handlers go through it,
 * so normalization, junk filtering and the per-shop row cap behave identically everywhere.
 */
final class CspViolationRecorder
{
    public const DEFAULT_ROW_CAP = 1000;

    private const MAX_DOCUMENT_URI_LENGTH = 2048;

    public function __construct(
        private readonly CspLogRepository $repository,
        private readonly int $rowCap = self::DEFAULT_ROW_CAP,
    ) {
    }

    /**
     * Records one reported violation. Unknown directives and junk/invalid sources are dropped
     * silently — the input is untrusted browser data, not a user action to reject.
     */
    public function record(int $shopId, string $rawDirective, string $rawSource, ?string $documentUri): void
    {
        $directiveName = CspReportNormalizer::normalizeDirective($rawDirective);
        if (null === $directiveName) {
            return;
        }

        $directive = CspDirective::tryFrom($directiveName);
        if (null === $directive) {
            return;
        }

        $sourceExpression = CspReportNormalizer::normalizeSource($rawSource);
        if (null === $sourceExpression) {
            return;
        }

        try {
            $source = new CspSource($sourceExpression);
        } catch (CspConstraintException) {
            return;
        }

        $inserted = $this->repository->upsert($shopId, $directive->value, $source->getValue(), $this->normalizeDocumentUri($documentUri));

        // Only a fresh insert can push the shop over the cap; a bumped hit counter leaves the row
        // count unchanged, so the common repeat-report flood skips the COUNT entirely.
        if ($inserted) {
            $this->enforceRowCap($shopId);
        }
    }

    public function clear(int $shopId): void
    {
        $this->repository->deleteByShop($shopId);
    }

    private function normalizeDocumentUri(?string $documentUri): ?string
    {
        if (null === $documentUri) {
            return null;
        }

        $documentUri = trim($documentUri);
        if ($documentUri === '') {
            return null;
        }

        return mb_substr($documentUri, 0, self::MAX_DOCUMENT_URI_LENGTH);
    }

    private function enforceRowCap(int $shopId): void
    {
        $count = $this->repository->countByShop($shopId);
        if ($count > $this->rowCap) {
            $this->repository->deleteOldestByShop($shopId, $count - $this->rowCap);
        }
    }
}
