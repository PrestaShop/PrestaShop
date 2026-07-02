<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Catalog;

use JsonSerializable;

/**
 * One column of a back-office grid, as exposed by the grid catalog.
 */
final class GridColumnEntry implements JsonSerializable
{
    public function __construct(
        public readonly string $id,
        public readonly string $label,
        public readonly int $position,
    ) {
    }

    /**
     * @return array{id: string, label: string, position: int}
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'position' => $this->position,
        ];
    }
}
