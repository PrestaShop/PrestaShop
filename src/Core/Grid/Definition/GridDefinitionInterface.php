<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;

/**
 * Interface GridDefinitionInterface defines contract for grid definition.
 */
interface GridDefinitionInterface
{
    /**
     * Get unique grid identifier.
     *
     * @return string
     */
    public function getId();

    /**
     * Get grid name.
     *
     * @return string
     */
    public function getName();

    /**
     * Get grid columns.
     *
     * @return ColumnCollectionInterface
     */
    public function getColumns();

    /**
     * @param string $id
     *
     * @return ColumnInterface
     */
    public function getColumnById(string $id): ColumnInterface;

    /**
     * @return BulkActionCollectionInterface
     */
    public function getBulkActions();

    /**
     * Get grid actions.
     *
     * @return GridActionCollectionInterface
     */
    public function getGridActions();

    /**
     * @return ViewOptionsCollectionInterface
     */
    public function getViewOptions();

    /**
     * Get filters.
     *
     * @return FilterCollectionInterface
     */
    public function getFilters();
}
