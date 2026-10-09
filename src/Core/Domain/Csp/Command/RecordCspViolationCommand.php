<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Command;

use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;

/**
 * Records a single browser-reported CSP violation from raw report fields;
 * the recorder normalizes, filters and validates.
 * Takes a concrete $shopId, not a ShopConstraint: a report always belongs to the single surface that fired it
 * (the global back office always uses shop id 0).
 */
final class RecordCspViolationCommand
{
    public function __construct(
        private readonly string $directive,
        private readonly string $source,
        private readonly ?string $documentUri,
        private readonly int $shopId,
        private readonly CspContext $context = CspContext::FRONT,
        private readonly ?string $sample = null,
        private readonly ?string $sourceFile = null,
        private readonly ?int $lineNumber = null,
    ) {
    }

    public function getContext(): CspContext
    {
        return $this->context;
    }

    public function getDirective(): string
    {
        return $this->directive;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function getDocumentUri(): ?string
    {
        return $this->documentUri;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getSample(): ?string
    {
        return $this->sample;
    }

    public function getSourceFile(): ?string
    {
        return $this->sourceFile;
    }

    public function getLineNumber(): ?int
    {
        return $this->lineNumber;
    }
}
