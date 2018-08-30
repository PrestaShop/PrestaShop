<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\InvalidDataException;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;

/**
 * Class Definition is responsible for storing grid definition (columns, row actions & etc.).
 */
final class GridDefinition implements GridDefinitionInterface
{
    /**
     * @var string Unique grid identifier
     */
    private $id;

    /**
     * @var string Grid name
     */
    private $name;

    /**
     * @var ColumnInterface[]
     */
    private $columns;

    /**
     * @var GridActionCollectionInterface
     */
    private $gridActions;

    /**
     * @var BulkActionCollectionInterface
     */
    private $bulkActions;

    /**
     * @var FilterCollectionInterface
     */
    private $filters;

    /**
     * @param string $id Unique grid identifier
     * @param string $name
     * @param ColumnCollectionInterface $columns
     * @param FilterCollectionInterface $filters
     * @param GridActionCollectionInterface $gridActions
     * @param BulkActionCollectionInterface $bulkActions
     */
    public function __construct(
        $id,
        $name,
        ColumnCollectionInterface $columns,
        FilterCollectionInterface $filters,
        GridActionCollectionInterface $gridActions,
        BulkActionCollectionInterface $bulkActions
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->columns = $columns;
        $this->filters = $filters;
        $this->gridActions = $gridActions;
        $this->bulkActions = $bulkActions;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkActions()
    {
        return $this->bulkActions;
    }

    /**
     * {@inheritdoc}
     */
    public function getGridActions()
    {
        return $this->gridActions;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        if (!is_string($name)) {
            throw new InvalidDataException('Definition name should be a string.');
        }

        $this->name = $name;
    }

    /**
     * @param ColumnCollectionInterface $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }

    /**
     * @param GridActionCollectionInterface $gridActions
     */
    public function setGridActions(GridActionCollectionInterface $gridActions)
    {
        $this->gridActions = $gridActions;
    }

    /**
     * @param BulkActionCollectionInterface $bulkActions
     */
    public function setBulkActions(BulkActionCollectionInterface $bulkActions)
    {
        $this->bulkActions = $bulkActions;
    }

    /**
     * @param FilterCollectionInterface $filters
     */
    public function setFilters(FilterCollectionInterface $filters)
    {
        $this->filters = $filters;
    }
}
