<?php
/**
 * 2007-2018 PrestaShop
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

use PrestaShop\PrestaShop\Core\Grid\Action\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\BulkActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;

/**
 * Class Definition is responsible for storing grid definition (columns, row actions & etc.)
 */
final class Definition implements GridDefinitionInterface
{
    /**
     * @var string  Grid name
     */
    private $name;

    /**
     * @var string  Unique grid identifier
     */
    private $identifier;

    /**
     * @var ColumnCollectionInterface
     */
    private $columns;

    /**
     * @var RowActionCollectionInterface
     */
    private $rowActions;

    /**
     * @var BulkActionCollectionInterface
     */
    private $bulkActions;

    /**
     * @var GridActionCollectionInterface
     */
    private $gridActions;

    /**
     * @param string $identifier      Unique grid identifier (used as table ID when rendering table)
     * @param string $name            Translated grid name
     */
    public function __construct($identifier, $name)
    {
        $this->identifier = $identifier;
        $this->name = $name;

        $this->rowActions = new RowActionCollection();
        $this->columns = new ColumnCollection();
        $this->bulkActions = new BulkActionCollection();
        $this->gridActions = new GridActionCollection();
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
    public function getIdentifier()
    {
        return $this->identifier;
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
    public function getRowActions()
    {
        return $this->rowActions;
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
     * @param ColumnCollectionInterface $columns
     */
    public function setColumns(ColumnCollectionInterface $columns)
    {
        $this->columns = $columns;
    }

    /**
     * @param RowActionCollectionInterface $rowActions
     */
    public function setRowActions(RowActionCollectionInterface $rowActions)
    {
        $this->rowActions = $rowActions;
    }

    /**
     * @param BulkActionCollectionInterface $bulkActions
     */
    public function setBulkActions(BulkActionCollectionInterface $bulkActions)
    {
        $this->bulkActions = $bulkActions;
    }

    /**
     * @param GridActionCollectionInterface $gridActions
     */
    public function setGridActions(GridActionCollectionInterface $gridActions)
    {
        $this->gridActions = $gridActions;
    }
}
