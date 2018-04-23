<?php

namespace PrestaShop\PrestaShop\Core\Table;
use Symfony\Component\Form\FormView;

/**
 * Class TableView is responsible for storing table data that is passed to template to render table
 */
final class TableView
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
     * @var FormView
     */
    private $formView;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $rows;

    /**
     * @var int
     */
    private $rowsTotal;

    /**
     * @param string    $identifier     Table identifier should be unique per table and will act as ID on html table element
     * @param string    $name           Table name
     * @param array     $columnViews    Table columns
     * @param array     $rowViews       Table rows data
     * @param int       $rowsTotal      Total count of all rows
     * @param FormView  $formView       Filters form view
     */
    public function __construct($identifier, $name, array $columnViews, array $rowViews, $rowsTotal, FormView $formView)
    {
        $this->columns = $columnViews;
        $this->rows = $rowViews;
        $this->identifier = $identifier;
        $this->rowsTotal = $rowsTotal;
        $this->name = $name;
        $this->formView = $formView;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return FormView
     */
    public function getFormView()
    {
        return $this->formView;
    }
}
