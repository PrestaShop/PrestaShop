<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Repository;

use DateTimeImmutable;

/**
 * Narrow import-specific fallback writer covering the two things the CQRS
 * product commands deliberately cannot express:
 * - creating a product with a caller-chosen id (the "force IDs" option)
 * - setting date_add (date_upd is always forced to now, legacy parity)
 *
 * Everything else an importer persists MUST go through CQRS commands.
 *
 * @internal only meant for internal use by the Import engine components,
 *           not to be overridden or decorated
 */
interface ProductImportWriterInterface
{
    /**
     * Creates a minimal product shell with the given forced id, mirroring the
     * AddProductCommand handler defaults (inactive, shop default category,
     * most-used tax rules group). The importer then applies every mapped field
     * through the regular commands.
     *
     * @param array<int, string> $localizedNames indexed by language id
     */
    public function createProductWithId(int $forcedProductId, string $productType, int $shopId, array $localizedNames): void;

    public function setDateAdd(int $productId, DateTimeImmutable $dateAdd): void;
}
