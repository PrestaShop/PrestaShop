<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product;

/**
 * Update-vs-create decision for one product row.
 */
final class ProductMatch
{
    public const MATCHED_BY_REFERENCE = 'reference';
    public const MATCHED_BY_ID = 'id';

    /**
     * @param int|null $productId existing product to update, null to create
     * @param string|null $matchedBy MATCHED_BY_* constant when updating
     * @param int|null $forcedId id to force on creation (force IDs option)
     */
    public function __construct(
        public readonly ?int $productId,
        public readonly ?string $matchedBy = null,
        public readonly ?int $forcedId = null,
    ) {
    }

    public function isUpdate(): bool
    {
        return null !== $this->productId;
    }
}
