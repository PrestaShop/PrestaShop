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

namespace PrestaShop\PrestaShop\Core\Grid;

use PrestaShop\PrestaShop\Core\Grid\Action\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Column;
use PrestaShop\PrestaShop\Core\Grid\Action\RowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use PrestaShop\PrestaShop\Core\Grid\Exception\ColumnsNotDefinedException;
use Symfony\Component\Form\FormInterface;

/**
 * Class Grid is responsible for holding grid's data
 */
final class Grid
{
    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $name;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var ColumnCollectionInterface
     */
    private $columns;

    /**
     * @var RowActionCollectionInterface
     */
    private $rowActions;

    /**
     * @var array
     */
    private $rows = [];

    /**
     * @var int
     */
    private $rowsTotal = 0;

    /**
     * @param GridDefinitionInterface $gridDefinition
     * @param FormInterface           $form
     *
     * @throws ColumnsNotDefinedException When definition does not define any columns for grid
     */
    public function __construct(GridDefinitionInterface $gridDefinition, FormInterface $form)
    {
        if (0 == count($gridDefinition->getColumns())) {
            throw new ColumnsNotDefinedException(
                sprintf('Grid "%s" definition does not contain any columns', $gridDefinition->getIdentifier())
            );
        }

        $this->identifier = $gridDefinition->getIdentifier();
        $this->name = $gridDefinition->getName();
        $this->columns = $gridDefinition->getColumns();
        $this->rowActions = $gridDefinition->getRowActions();
        $this->form = $form;
    }

    /**
     * Set rows for grid
     *
     * @param array $rows
     *
     * @return $this
     */
    public function setRows(array $rows)
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * Set total count of all rows
     *
     * @param int $rowsTotal
     *
     * @return $this
     */
    public function setRowsTotal($rowsTotal)
    {
        $this->rowsTotal = $rowsTotal;

        return $this;
    }

    /**
     * @return ColumnCollectionInterface
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return RowActionCollectionInterface
     */
    public function getRowActions()
    {
        return $this->rowActions;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return int
     */
    public function getRowsTotal()
    {
        return $this->rowsTotal;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return FormInterface
     */
    public function getFilterForm()
    {
        return $this->form;
    }
}
