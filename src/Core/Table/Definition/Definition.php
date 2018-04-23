<?php

namespace PrestaShop\PrestaShop\Core\Table\Definition;

use PrestaShop\PrestaShop\Core\Table\Column;
use PrestaShop\PrestaShop\Core\Table\Exception\NonUniqueColumnException;
use PrestaShop\PrestaShop\Core\Table\Exception\NonUniqueRowActionException;
use PrestaShop\PrestaShop\Core\Table\RowAction;

/**
 * Class Definition is responsible for storing table definition (columns, row actions & etc.)
 */
final class Definition implements TableDefinitionInterface
{
    /**
     * @var string  Table name
     */
    private $name;

    /**
     * @var string  Unique table idetifier
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
     * @param string $identifier        Unique table identifier (used as table ID when rendering table)
     * @param string $name              Translated table name
     * @param string $defaultOrderBy    Default table ordering by
     * @param string $defaultOrderWay   Default table ordering way
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
     */
    public function addColumn(Column $column)
    {
        if (isset($this->columns[$column->getIdentifier()])) {
            throw new NonUniqueColumnException(sprintf('Duplicated column "%s" on table definition'));
        }

        $this->columns[$column->getIdentifier()] = $column;
    }

    /**
     * {@inheritdoc}
     */
    public function addRowAction(RowAction $rowAction)
    {
        if (isset($this->rowActions[$rowAction->getIdentifier()])) {
            throw new NonUniqueRowActionException(sprintf('Row action "%s" already exsists on table definition'));
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
