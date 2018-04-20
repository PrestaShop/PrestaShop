<?php

namespace PrestaShop\PrestaShop\Core\Table\Definition;

use PrestaShop\PrestaShop\Core\Table\Column;
use PrestaShop\PrestaShop\Core\Table\RowAction;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RequestSqlTableDefinition is responsible for defining Request SQL table
 */
final class RequestSqlTableDefinition implements TableDefinitionInterface
{
    /**
     * @var array|Column[]
     */
    private $addedColumns = [];

    /**
     * @var array|RowAction[]
     */
    private $addedRowActions = [];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Add new column to table definition
     *
     * @param Column $column
     *
     * @return RequestSqlTableDefinition
     */
    public function addColumn(Column $column)
    {
        $this->addedColumns[] = $column;

        return $this;
    }

    /**
     * @param RowAction $rowAction
     *
     * @return RequestSqlTableDefinition
     */
    public function addRowAction(RowAction $rowAction)
    {
        $this->addedRowActions[] = $rowAction;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->translator->trans('Request Sql');
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return 'request_sql_table';
    }

    /**
     * @return string
     */
    public function getDefaultOrderBy()
    {
        return 'id_request_sql';
    }

    /**
     * @return string
     */
    public function getDefaultOrderWay()
    {
        return 'desc';
    }

    /**
     * @return array|Column[]
     */
    public function getColumns()
    {
        $columns = [
            (new Column('id_request_sql', $this->translator->trans('ID')))->setFormType(TextType::class),
            (new Column('name', $this->translator->trans('SQL query Name')))->setFormType(TextType::class),
            (new Column('sql', $this->translator->trans('SQL query')))->setFormType(TextType::class),
        ];

        $columns = array_merge($columns, $this->addedColumns);

        return $columns;
    }

    /**
     * @return array|RowAction[]
     */
    public function getRowActions()
    {
        $dummyCallback = function ($rowData) {
            return 'http://some.url.generated.from.row.data';
        };

        $rowActions = [
            new RowAction('export', $this->translator->trans('Export'), $dummyCallback, 'cloud_download'),
            new RowAction('edit', $this->translator->trans('Edit'), $dummyCallback, 'edit')
        ];

        $rowActions = array_merge($rowActions, $this->addedRowActions);

        return $rowActions;
    }
}
