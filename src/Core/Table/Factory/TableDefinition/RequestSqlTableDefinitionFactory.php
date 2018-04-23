<?php

namespace PrestaShop\PrestaShop\Core\Table\Factory\TableDefinition;

use PrestaShop\PrestaShop\Core\Table\Column;
use PrestaShop\PrestaShop\Core\Table\Definition\Definition;
use PrestaShop\PrestaShop\Core\Table\RowAction;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class RequestSqlTableDefinitionFactory is responsible for creating table definition for Request SQL
 */
final class RequestSqlTableDefinitionFactory implements TableDefinitionFactoryInterface
{
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
     * {@inheritdoc}
     */
    public function createNew()
    {
        $definition = new Definition(
            'request_sql_table',
            $this->translator->trans('Request SQL'),
            'id_request_sql',
            'asc'
        );

        $this->addDefaultColumns($definition);
        $this->addDefaultRowActions($definition);

        return $definition;
    }

    /**
     * Add default columns for table definition
     *
     * @param Definition $definition
     */
    private function addDefaultColumns(Definition $definition)
    {
        $columns = [
            (new Column('id_request_sql', $this->translator->trans('ID')))->setFormType(TextType::class),
            (new Column('name', $this->translator->trans('SQL query Name')))->setFormType(TextType::class),
            (new Column('sql', $this->translator->trans('SQL query')))->setFormType(TextType::class),
        ];

        foreach ($columns as $column) {
            $definition->addColumn($column);
        }
    }

    /**
     * Add default row actions for table definition
     *
     * @param Definition $definition
     */
    private function addDefaultRowActions(Definition $definition)
    {
        $dummyCallback = function ($rowData) {
            return 'http://some.url.generated.from.row.data';
        };

        $rowActions = [
            new RowAction('export', $this->translator->trans('Export'), $dummyCallback, 'cloud_download'),
            new RowAction('edit', $this->translator->trans('Edit'), $dummyCallback, 'edit')
        ];

        foreach ($rowActions as $rowAction) {
            $definition->addRowAction($rowAction);
        }
    }
}
