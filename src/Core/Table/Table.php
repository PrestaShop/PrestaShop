<?php

namespace PrestaShop\PrestaShop\Core\Table;

use PrestaShop\PrestaShop\Core\Table\Definition\TableDefinitionInterface;
use PrestaShop\PrestaShop\Core\Table\Exception\ColumnsNotDefinedException;
use Symfony\Component\Form\FormInterface;

/**
 * Class Table is responsible for holding table's data
 */
final class Table
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
     * @var array|Column[]
     */
    private $columns;

    /**
     * @var array|RowAction[]
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
     * @param TableDefinitionInterface  $tableDefinition
     * @param FormInterface             $form
     *
     * @throws ColumnsNotDefinedException   When definition does not define any columns for table
     */
    public function __construct(TableDefinitionInterface $tableDefinition, FormInterface $form)
    {
        if (0 == count($tableDefinition->getColumns())) {
            throw new ColumnsNotDefinedException(
                sprintf('Table "%s" definition does not contain any columns', $tableDefinition->getIdentifier())
            );
        }

        $this->identifier = $tableDefinition->getIdentifier();
        $this->name = $tableDefinition->getName();
        $this->columns = $tableDefinition->getColumns();
        $this->rowActions = $tableDefinition->getRowActions();
        $this->form = $form;
    }

    /**
     * Set rows for table
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
     * @return array|Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array|RowAction[]
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
    public function getForm()
    {
        return $this->form;
    }
}
