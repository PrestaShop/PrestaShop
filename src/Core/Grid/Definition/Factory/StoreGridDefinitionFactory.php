<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Defines store grid
 */
class StoreGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;
    use BulkDeleteActionTrait;

    public const GRID_ID = 'store';

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Stores', [], 'Admin.Stores.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_store',
                    ])
            )
            ->add(
                (new DataColumn('id_store'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_store',
                    ])
            )
            ->add(
                (new DataColumn('address1'))
                    ->setName($this->trans('Address 1', [], 'Admin.Stores.Feature'))
                    ->setOptions([
                        'field' => 'address1',
                    ])
            )
            ->add(
                (new DataColumn('address2'))
                    ->setName($this->trans('Address 2', [], 'Admin.Stores.Feature'))
                    ->setOptions([
                        'field' => 'address2',
                    ])
            )
            ->add(
                (new DataColumn('city'))
                    ->setName($this->trans('City', [], 'Admin.Stores.Feature'))
                    ->setOptions([
                        'field' => 'city',
                    ])
            )
            ->add(
                (new DataColumn('postcode'))
                    ->setName($this->trans('Post Code', [], 'Admin.Stores.Feature'))
                    ->setOptions([
                        'field' => 'postcode',
                    ])
            )
            ->add(
                (new DataColumn('state_name'))
                    ->setName($this->trans('State', [], 'Admin.Stores.Feature'))
                    ->setOptions([
                        'field' => 'state_name',
                    ])
            )
            ->add(
                (new DataColumn('country_name'))
                    ->setName($this->trans('Country', [], 'Admin.Stores.Feature'))
                    ->setOptions([
                        'field' => 'country_name',
                    ])
            )
            ->add(
                (new DataColumn('phone'))
                    ->setName($this->trans('Phone', [], 'Admin.Stores.Feature'))
                    ->setOptions([
                        'field' => 'phone',
                    ])
            )
            ->add(
                (new DataColumn('fax'))
                    ->setName($this->trans('Fax', [], 'Admin.Stores.Feature'))
                    ->setOptions([
                        'field' => 'fax',
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_store',
                        'route' => 'admin_stores_toggle_status',
                        'route_param_name' => 'storeId',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => $this->getRowActions(),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_store', TextType::class))
                    ->setAssociatedColumn('id_store')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setAssociatedColumn('name')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('address1', TextType::class))
                    ->setAssociatedColumn('address1')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search address 1', [], 'Admin.Stores.Feature'),
                        ],
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('address2', TextType::class))
                    ->setAssociatedColumn('address2')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search address 2', [], 'Admin.Stores.Feature'),
                        ],
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('city', TextType::class))
                    ->setAssociatedColumn('city')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search city', [], 'Admin.Stores.Feature'),
                        ],
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('postcode', TextType::class))
                    ->setAssociatedColumn('postcode')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search post code', [], 'Admin.Stores.Feature'),
                        ],
                        'required' => false,
                    ])
            )
            /* -- TODO -> State Choice Type --
            ->add(
                (new Filter('id_state', StateChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('state_name')
            )*/
            ->add(
                (new Filter('id_state', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('state_name')
            )
            ->add(
                (new Filter('id_country', CountryChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('country_name')
            )
            ->add(
                (new Filter('phone', TextType::class))
                    ->setAssociatedColumn('phone')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search phone', [], 'Admin.Stores.Feature'),
                        ],
                        'required' => false,
                    ])
            )  
            ->add(
                (new Filter('fax', TextType::class))
                    ->setAssociatedColumn('fax')
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search fax', [], 'Admin.Stores.Feature'),
                        ],
                        'required' => false,
                    ])
            )    
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_stores_index',
                    ])
            );
    }

    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                    ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                    ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                    ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                    ->setIcon('storage')
            );
    }

    private function getRowActions(): RowActionCollectionInterface
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_stores_edit',
                        'route_param_name' => 'storeId',
                        'route_param_field' => 'id_store',
                        'clickable_row' => true,
                    ])
            )
            ->add(
                $this->buildDeleteAction(
                    'admin_stores_delete',
                    'storeId',
                    'id_store'
                )
            );
    }

    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_stores_bulk_enable_status',
                    ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_stores_bulk_disable_status',
                    ])
            )
            ->add(
                $this->buildBulkDeleteAction('admin_stores_bulk_delete')
            );
    }
}
