<?php

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CustomerGroupsGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'customer_groups';

    protected function getId()
    {
        return self::GRID_ID;
    }

    protected function getName()
    {
        return $this->trans('Customer Groups', [], 'Admin.Navigation.Menu');
    }

    protected function getColumns()
    {

        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('title_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_group',
                    ])
            )
            ->add(
                (new DataColumn('id_group'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_group',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('reduction'))
                    ->setName($this->trans('Reduction', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'reduction',
                    ])
            )
            ->add(
                (new DataColumn('price_display_method'))
                    ->setName($this->trans('Price display Method', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'price_display_method',
                    ])
            )
            ->add(
                (new DataColumn('show_prices'))
                    ->setName($this->trans('Show prices', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'show_prices',
                    ])
            )
        ;
    }


    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_group', NumberType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->translator->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('id_group')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->translator->trans('Search title', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_customer_groups_index',
                    ])
                    ->setAssociatedColumn('actions')
            )
            ;
    }
}
