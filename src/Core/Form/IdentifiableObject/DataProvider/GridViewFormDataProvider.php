<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\GridView\Query\GetGridViewForEditing;
use PrestaShop\PrestaShop\Core\Domain\GridView\QueryResult\EditableGridView;

/**
 * Provides the data of the grid view edit form.
 */
final class GridViewFormDataProvider implements FormDataProviderInterface
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
    public function getData($id): array
    {
        /** @var EditableGridView $editableGridView */
        $editableGridView = $this->queryBus->handle(new GetGridViewForEditing((int) $id));

        return [
            'name' => $editableGridView->getName(),
            'shared' => $editableGridView->isShared(),
            'dynamic_date_rules' => $editableGridView->getDynamicDateRules(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return [];
    }
}
