<?php



namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;

final class SeoUrlsGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'seo_urls';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Seo & urls', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_meta',
                ])
            )
            ->add((new DataColumn('id_request_sql'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_meta',
                ])
            )
            ->add((new DataColumn('page'))
                ->setName($this->trans('Page', [], 'Admin.Shopparameters.Feature'))
                ->setOptions([
                    'field' => 'page',
                ])
            )
        ;
    }
}
