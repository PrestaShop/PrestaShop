<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

use PrestaShop\PrestaShop\Core\Grid\GridInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;

final class GridState
{
    private const DATE_COLUMN_TYPES = ['date', 'date_time'];

    /**
     * @param ColumnState[] $columns
     * @param array<string, mixed> $filters filter values indexed by field name
     */
    public function __construct(
        public readonly string $gridId,
        public readonly string $filterId,
        public readonly array $columns,
        public readonly array $filters,
    ) {
    }

    /**
     * @param GridInterface $grid
     *
     * @return GridState
     */
    public static function fromGrid(GridInterface $grid): self
    {
        $searchCriteria = $grid->getSearchCriteria();
        $filterId = $searchCriteria instanceof Filters ? $searchCriteria->getFilterId() : $grid->getDefinition()->getId();

        $columns = [];
        foreach ($grid->getDefinition()->getColumns() as $column) {
            $columns[] = ColumnState::fromColumn($column);
        }

        return new self(
            $grid->getDefinition()->getId(),
            $filterId,
            $columns,
            $searchCriteria->getFilters(),
        );
    }

    /**
     * @param array $data
     *
     * @return GridState
     */
    public static function fromArray(array $data): self
    {
        $columns = [];
        foreach ($data['columns'] ?? [] as $column) {
            if (!is_array($column) || !isset($column['id'], $column['name'], $column['type'])) {
                continue;
            }

            $columns[] = new ColumnState(
                (string) $column['id'],
                (string) $column['name'],
                (string) $column['type'],
            );
        }

        return new self(
            (string) ($data['grid_id'] ?? ''),
            (string) ($data['filter_id'] ?? ''),
            $columns,
            is_array($data['filters'] ?? null) ? $data['filters'] : [],
        );
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'grid_id' => $this->gridId,
            'filter_id' => $this->filterId,
            'columns' => array_map(static fn (ColumnState $column) => $column->toArray(), $this->columns),
            'filters' => $this->filters,
        ];
    }

    /**
     * @return array<string, array{id: string, name: string}> indexed by field name
     */
    public function getActiveDateRangeFilters(): array
    {
        $activeDateFilters = [];

        foreach ($this->columns as $column) {
            if (!in_array($column->type, self::DATE_COLUMN_TYPES, true)) {
                continue;
            }

            $filterValue = $this->filters[$column->id] ?? null;
            if (!is_array($filterValue) || (!isset($filterValue['from']) && !isset($filterValue['to']))) {
                continue;
            }

            $activeDateFilters[$column->id] = [
                'id' => $column->id,
                'name' => $column->name,
            ];
        }

        return $activeDateFilters;
    }
}
