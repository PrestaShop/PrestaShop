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

use PrestaShop\PrestaShop\Core\Grid\Action\Column;
use PrestaShop\PrestaShop\Core\Grid\Action\RowAction;
use PrestaShop\PrestaShop\Core\Grid\Exception\NonUniqueColumnException;
use PrestaShop\PrestaShop\Core\Grid\Exception\NonUniqueRowActionException;

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
     * @var string  Unique grid idetifier
     */
    private $identifier;

    /**
     * @var string
     */
    private $defaultOrderBy;

    /**
     * @var string
     */
    private $defaultOrderWay;

    /**
     * @var array|Column[]
     */
    private $columns = [];

    /**
     * @var array|RowAction[]
     */
    private $rowActions = [];

    /**
     * @param string $identifier      Unique grid identifier (used as table ID when rendering table)
     * @param string $name            Translated grid name
     * @param string $defaultOrderBy  Default grid ordering by
     * @param string $defaultOrderWay Default grid ordering way
     */
    public function __construct($identifier, $name, $defaultOrderBy, $defaultOrderWay)
    {
        $this->identifier = $identifier;
        $this->name = $name;
        $this->defaultOrderBy = $defaultOrderBy;
        $this->defaultOrderWay = $defaultOrderWay;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueColumnException
     */
    public function addColumn(Column $column)
    {
        if (isset($this->columns[$column->getIdentifier()])) {
            throw new NonUniqueColumnException(sprintf('Duplicated column "%s" on grid definition'));
        }

        $this->columns[$column->getIdentifier()] = $column;
    }

    /**
     * {@inheritdoc}
     *
     * @throws NonUniqueRowActionException
     */
    public function addRowAction(RowAction $rowAction)
    {
        if (isset($this->rowActions[$rowAction->getIdentifier()])) {
            throw new NonUniqueRowActionException(sprintf('Row action "%s" already exsists on grid definition'));
        }

        $this->rowActions[$rowAction->getIdentifier()] = $rowAction;
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
    public function getDefaultOrderBy()
    {
        return $this->defaultOrderBy;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOrderWay()
    {
        return $this->defaultOrderWay;
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
}
