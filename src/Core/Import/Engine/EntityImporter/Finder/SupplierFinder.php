<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder;

use PrestaShop\PrestaShop\Adapter\Supplier\Repository\SupplierRepository;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;

/**
 * MATCH-ONLY: suppliers are never auto-created by the import (a supplier
 * requires an address, which the file cannot provide) — an empty result means
 * the caller drops the field with a warning.
 *
 * Deliberate quirk kept from the legacy behavior: a numeric value whose id
 * does not exist falls through to the NAME lookup (unlike manufacturers and
 * categories, where an unknown numeric id is a hard miss).
 */
class SupplierFinder
{
    public function __construct(
        protected readonly SupplierRepository $supplierRepository,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
    ) {
    }

    public function find(string $value): EntityLookupResult
    {
        if (ctype_digit($value) && $this->existenceChecker->exists('supplier', (int) $value)) {
            return new EntityLookupResult([['id' => (int) $value, 'matchedBy' => EntityLookupResult::MATCHED_BY_ID]]);
        }

        return new EntityLookupResult(array_map(
            static fn (int $supplierId): array => ['id' => $supplierId, 'matchedBy' => EntityLookupResult::MATCHED_BY_NAME],
            $this->supplierRepository->getSupplierIdsByName($value)
        ));
    }
}
