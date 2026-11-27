<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\GridView\QueryHandler;

use PrestaShop\PrestaShop\Adapter\GridView\GridViewProvider;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\GridView\Query\GetGridViewForEditing;
use PrestaShop\PrestaShop\Core\Domain\GridView\QueryHandler\GetGridViewForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\GridView\QueryResult\EditableGridView;
use PrestaShop\PrestaShop\Core\Grid\View\GridState;
use PrestaShopBundle\Entity\AdminGridView;

#[AsQueryHandler]
final class GetGridViewForEditingHandler implements GetGridViewForEditingHandlerInterface
{
    public function __construct(
        private readonly GridViewProvider $gridViewProvider,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetGridViewForEditing $query): EditableGridView
    {
        $gridView = $this->gridViewProvider->getOwnedGridView($query->getGridViewId());

        return new EditableGridView(
            $gridView->getId(),
            $gridView->getGridConfiguration()->getGridId(),
            $gridView->getName(),
            $gridView->isShared(),
            $gridView->getDynamicDateRules() ?? [],
            $this->getDateRangeFilterFields($gridView)
        );
    }

    /**
     * Extracts the date-range filters stored in the view, labelled with the column
     * names captured in the grid state when available.
     *
     * @param AdminGridView $gridView
     *
     * @return array<string, array{id: string, name: string}>
     */
    private function getDateRangeFilterFields(AdminGridView $gridView): array
    {
        $searchCriteria = json_decode($gridView->getFilters(), true) ?: [];
        $gridState = GridState::fromArray($gridView->getGridState() ?? []);

        $columnNames = [];
        foreach ($gridState->columns as $column) {
            $columnNames[$column->id] = $column->name;
        }

        $dateRangeFilterFields = [];
        foreach ($searchCriteria['filters'] ?? [] as $field => $value) {
            if (!is_array($value) || (!isset($value['from']) && !isset($value['to']))) {
                continue;
            }

            $dateRangeFilterFields[$field] = [
                'id' => $field,
                'name' => $columnNames[$field] ?? $field,
            ];
        }

        return $dateRangeFilterFields;
    }
}
