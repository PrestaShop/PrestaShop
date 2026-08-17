<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\GridView\Query\GetGridViewForEditing;
use PrestaShop\PrestaShop\Core\Domain\GridView\QueryResult\EditableGridView;

/**
 * Provides the options of the grid view edit form: the grid context fields (route, filter id,
 * grid state) are only submitted on creation, and the dynamic date rule fields are built from
 * the date-range filters stored in the edited view.
 */
final class GridViewFormOptionsProvider implements FormOptionsProviderInterface
{
    /**
     * @param CommandBusInterface $queryBus
     */
    public function __construct(
        private readonly CommandBusInterface $queryBus,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions(int $id, array $data): array
    {
        /** @var EditableGridView $editableGridView */
        $editableGridView = $this->queryBus->handle(new GetGridViewForEditing($id));

        return [
            'with_grid_context' => false,
            'active_date_filters' => $editableGridView->getDateRangeFilterFields(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $data): array
    {
        return [];
    }
}
