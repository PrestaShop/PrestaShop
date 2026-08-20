<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\PositiveLookupCacheTrait;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;

/**
 * MATCH-ONLY: resolves one shop cell entry (numeric id or name) to shop ids;
 * shops are obviously never created by an import. Soft-deleted shops count as
 * absent on both branches (the probe and the name lookup both filter them).
 *
 * BOTH branches are memoized, by different caches, because they run different
 * queries: the id branch through ImportEntityExistenceChecker (which keeps its
 * own '<table>:<id>' memo, shared with every other importer), the name branch
 * through PositiveLookupCacheTrait. Wrapping the id branch in remember() too
 * would just stack a second cache on top of the first. Both cache POSITIVE
 * results only, and both skip the QUERY only — the caller still re-reads the
 * full result, and may re-warn, on every row.
 */
class ShopFinder implements EntityFinderInterface
{
    use PositiveLookupCacheTrait;

    public function __construct(
        protected readonly ShopRepository $shopRepository,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
    ) {
    }

    /**
     * Shop names are deliberately looked up GLOBALLY, so the run's scope plays
     * no part here — $context is only present to satisfy the shared contract.
     */
    public function find(string $value, ImportRunContext $context): FoundEntity
    {
        if (ctype_digit($value)) {
            return new FoundEntity(
                $this->existenceChecker->exists('shop', (int) $value)
                    ? [['id' => (int) $value, 'matchedBy' => FoundEntity::MATCHED_BY_ID]]
                    : []
            );
        }

        return new FoundEntity(array_map(
            static fn (int $shopId): array => ['id' => $shopId, 'matchedBy' => FoundEntity::MATCHED_BY_NAME],
            $this->remember($value, fn (): array => $this->shopRepository->getShopIdsByName($value))
        ));
    }
}
