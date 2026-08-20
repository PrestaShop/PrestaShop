<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder;

use PrestaShop\PrestaShop\Adapter\Supplier\Repository\SupplierRepository;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\PositiveLookupCacheTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * MATCH-ONLY: suppliers are never auto-created by the import (a supplier
 * requires an address, which the file cannot provide) — an empty result means
 * the caller drops the field with a warning.
 *
 * Deliberate quirk kept from the legacy behavior: a numeric value whose id
 * does not exist falls through to the NAME lookup (unlike manufacturers and
 * categories, where an unknown numeric id is a hard miss).
 *
 * Both branches are memoized by different caches, because they run different
 * queries: the id branch through ImportEntityExistenceChecker's own
 * '<table>:<id>' memo, the name branch through PositiveLookupCacheTrait (import
 * files repeat the same supplier on every line). Both cache POSITIVE results
 * only, and both skip the QUERY only — the caller still re-reads the full
 * result, and may re-warn, on every row.
 */
class SupplierFinder implements EntityFinderInterface
{
    use PositiveLookupCacheTrait;

    public function __construct(
        protected readonly SupplierRepository $supplierRepository,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
    ) {
    }

    /**
     * Supplier names are deliberately looked up GLOBALLY, so the run's scope
     * plays no part here — $context is only present to satisfy the shared
     * contract.
     */
    public function find(string $value, ImportRunContext $context): FoundEntity
    {
        if (ctype_digit($value) && $this->existenceChecker->exists('supplier', (int) $value)) {
            return new FoundEntity([['id' => (int) $value, 'matchedBy' => FoundEntity::MATCHED_BY_ID]]);
        }

        return new FoundEntity(array_map(
            static fn (int $supplierId): array => ['id' => $supplierId, 'matchedBy' => FoundEntity::MATCHED_BY_NAME],
            $this->remember($value, fn (): array => $this->supplierRepository->getSupplierIdsByName($value))
        ));
    }
}
