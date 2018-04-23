<?php

namespace PrestaShop\PrestaShop\Core\Table\Factory;

use PrestaShop\PrestaShop\Core\Table\Definition\TableDefinitionInterface;
use PrestaShop\PrestaShop\Core\Table\Table;
use PrestaShopBundle\Service\Hook\HookDispatcher;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TableFactory is responsible for creating table from it's definition
 */
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
     * {@inheritdoc}
     */
    public function createFromDefinition(TableDefinitionInterface $tableDefinition, Request $request) {
        // Execute hook to allow developers to modify/extend table definition
        // For exmaple add new columns, row actions & etc.
        $this->dispatcher->dispatchForParameters('modifyTableDefinition', [
            'table_definition' => $tableDefinition,
        ]);

        $filtersForm = $this->buildTableFilterForm($tableDefinition);
        $filtersForm->handleRequest($request);

        $table = new Table($tableDefinition, $filtersForm);

        return $table;
    }

    /**
     * Builds filters form for table
     *
     * @param TableDefinitionInterface $table
     *
     * @return FormInterface
     */
    private function buildTableFilterForm(TableDefinitionInterface $table)
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
