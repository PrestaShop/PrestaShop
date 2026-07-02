<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

/**
 * Enumerates the back-office grids an extra property definition can be associated with.
 */
interface GridCatalogInterface
{
    /**
     * @return list<GridCatalogEntry> sorted by label
     */
    public function getAll(): array;

    public function get(string $gridId): ?GridCatalogEntry;

    public function has(string $gridId): bool;
}
