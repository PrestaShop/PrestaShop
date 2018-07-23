<?php

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

/**
 * Class EmailLogsDefinitionFactory is responsible for creating email logs definition
 */
final class EmailLogsDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'email_logs';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('E-mail', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_mail'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_mail',
                ])
            )
            ->add((new DataColumn('recipient'))
                ->setName($this->trans('Recipient', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'recipient',
                ])
            )
            ->add((new DataColumn('template'))
                ->setName($this->trans('Template', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'template',
                ])
            )
            ->add((new DataColumn('language'))
                ->setName($this->trans('Language', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'language',
                ])
            )
            ->add((new DataColumn('subject'))
                ->setName($this->trans('Subject', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'subject',
                ])
            )
            ->add((new DataColumn('date_add'))
                ->setName($this->trans('Sent', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'date_add',
                ])
            )
        ;
    }
}
