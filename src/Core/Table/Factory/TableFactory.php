<?php

namespace PrestaShop\PrestaShop\Core\Table\Factory;

use PrestaShop\PrestaShop\Core\Table\Definition\TableDefinitionInterface;
use PrestaShop\PrestaShop\Core\Table\Table;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class TableFactory implements TableFactoryInterface
{
    /**
     * @var HookDispatcher
     */
    private $dispatcher;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param HookDispatcher $dispatcher
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        HookDispatcher $dispatcher,
        FormFactoryInterface $formFactory
    ) {
        $this->dispatcher = $dispatcher;
        $this->formFactory = $formFactory;
    }

    /**
     * Create table from it's definition
     *
     * @param TableDefinitionInterface $tableDefinition
     *
     * @return Table
     */
    public function createFromDefinition(TableDefinitionInterface $tableDefinition) {
        // Execute hook to allow developers to modify/extend table definition
        // For exmaple add new columns, row actions & etc.
        $this->dispatcher->dispatchForParameters('modifyTableDefinition', [
            'table_definition' => $tableDefinition,
        ]);

        $form = $this->getTableFilterForm($tableDefinition);
        $table = new Table($tableDefinition, $form);

        return $table;
    }

    /**
     * Create filters form for table
     *
     * @param TableDefinitionInterface $table
     *
     * @return FormInterface
     */
    private function getTableFilterForm(TableDefinitionInterface $table)
    {
        $formBuilder = $this->formFactory->createNamedBuilder($table->getIdentifier());

        foreach ($table->getColumns() as $column) {
            if ($formType = $column->getFormType()) {
                $options = $column->getFormTypeOptions();

                if (!isset($options['required'])) {
                    $options['required'] = false;
                }

                $formBuilder->add(
                    $column->getIdentifier(),
                    $formType,
                    $options
                );
            }
        }

        $form = $formBuilder
            ->setData([])
            ->getForm()
        ;

        return $form;
    }
}
