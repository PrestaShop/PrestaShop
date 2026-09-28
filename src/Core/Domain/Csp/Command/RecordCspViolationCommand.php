<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Csp\Command;

/**
 * Records a single browser-reported CSP violation from raw report fields;
 * the recorder normalizes, filters and validates.
 * Takes a concrete $shopId, not a ShopConstraint: a report always belongs to the single storefront that fired it.
 */
final class RecordCspViolationCommand
{
    public function __construct(
        private readonly string $directive,
        private readonly string $source,
        private readonly ?string $documentUri,
        private readonly int $shopId,
    ) {
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
}
