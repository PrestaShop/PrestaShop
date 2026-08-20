<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder;

use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ImportEntityExistenceChecker;

/**
 * MATCH-ONLY: resolves one shop cell entry (numeric id or name) to shop ids;
 * shops are obviously never created by an import. Soft-deleted shops count as
 * absent on both branches (the probe and the name lookup both filter them).
 *
 * Name lookups are cached for the service lifetime (one batch request) — the
 * cache skips the query only, so the caller re-reads the full result (and may
 * re-warn) on every row, as before.
 */
class ShopFinder
{
    /**
     * @var array<string, list<int>> shop name => every matching shop id
     */
    protected array $cache = [];

    public function __construct(
        protected readonly ShopRepository $shopRepository,
        protected readonly ImportEntityExistenceChecker $existenceChecker,
    ) {
    }

    public function find(string $entry): FoundEntity
    {
        if (ctype_digit($entry)) {
            return new FoundEntity(
                $this->existenceChecker->exists('shop', (int) $entry)
                    ? [['id' => (int) $entry, 'matchedBy' => FoundEntity::MATCHED_BY_ID]]
                    : []
            );
        }

        return new FoundEntity(array_map(
            static fn (int $shopId): array => ['id' => $shopId, 'matchedBy' => FoundEntity::MATCHED_BY_NAME],
            $this->cache[$entry] ??= $this->shopRepository->getShopIdsByName($entry)
        ));
    }
}
