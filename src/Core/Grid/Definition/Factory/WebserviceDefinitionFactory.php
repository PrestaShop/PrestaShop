<?php


namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

/**
 * Class WebserviceDefinitionFactory is responsible for creating grid definition for Webservice grid
 */
class WebserviceDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'webservice';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Webservice', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('key'))
                ->setName($this->trans('Key', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'key'
                ])
            )
            ->add((new DataColumn('description'))
                ->setName($this->trans('Key description', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'description'
                ])
            )
            ->add((new DataColumn('active'))
                ->setName($this->trans('Enabled', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active'
                ])
            );
    }
}
