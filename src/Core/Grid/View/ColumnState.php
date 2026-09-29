<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;

final class ColumnState
{
    /**
     * @param string $id
     * @param string $name
     * @param string $type
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $type,
    ) {
    }

    /**
     * @param ColumnInterface $column
     *
     * @return ColumnState
     */
    public static function fromColumn(ColumnInterface $column): self
    {
        return new self($column->getId(), $column->getName(), $column->getType());
    }

    /**
     * @return array{id: string, name: string, type: string}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
        ];
    }
}
