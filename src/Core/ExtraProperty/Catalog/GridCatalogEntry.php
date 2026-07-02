<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use JsonSerializable;

/**
 * One back-office grid, as exposed by the grid catalog.
 */
final class GridCatalogEntry implements JsonSerializable
{
    /**
     * @param string $id Grid definition id (GridDefinitionInterface::getId())
     * @param string $label Translated grid name
     * @param list<GridColumnEntry> $columns
     */
    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly array $columns,
    ) {
    }

    /**
     * @return array{id: string, label: string, columns: list<GridColumnEntry>}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'columns' => $this->columns,
        ];
    }
}
