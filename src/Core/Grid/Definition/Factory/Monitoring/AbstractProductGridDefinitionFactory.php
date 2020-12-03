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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Monitoring;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\IdentifierColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Defines reusable grids for product listing in monitoring page
 */
abstract class AbstractProductGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return $this::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new IdentifierColumn('id_product'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'identifier_field' => 'id_product',
                    ])
            )
            ->add(
                (new DataColumn('reference'))
                    ->setName($this->trans('Reference', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'reference',
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
                (new ToggleColumn('active'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_product',
                        'route' => 'admin_product_toggle_status',
                        'route_param_name' => 'productId',
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
        $filters = (new FilterCollection())
            ->add(
                (new Filter('id_product', TextType::class))
                    ->setAssociatedColumn('id_product')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('reference', TextType::class))
                    ->setAssociatedColumn('reference')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search reference', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setAssociatedColumn('name')
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                        ],
                    ])
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => $this::GRID_ID,
                        ],
                        'redirect_route' => 'admin_monitorings_index',
                    ])
                    ->setAssociatedColumn('actions')
            );

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            );
    }

    /**
     * @return RowActionCollection
     */
    protected function getRowActions()
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_product_form',
                        'route_param_name' => 'id',
                        'route_param_field' => 'id_product',
                    ])
            )
            ->add(
                (new LinkRowAction('delete'))
                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                    ->setIcon('delete')
                    ->setOptions([
                        'route' => 'admin_product_unit_action',
                        'route_param_name' => 'id',
                        'route_param_field' => 'id_product',
                        'extra_route_params' => ['action' => 'delete'],
                        'confirm_message' => $this->trans('Delete selected item?', [], 'Admin.Notifications.Warning'),
                    ])
            );
    }
}
