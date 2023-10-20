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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\EmptyColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Product\ShopNameColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;

/**
 * This grid definition is based on the product list one, it is used to display the details of each shop
 * for a specific product, by extending the initial definition we keep the same column and actions in every details.
 */
class ProductShopsGridDefinitionFactory extends ProductGridDefinitionFactory
{
    /**
     * We change the columns a little to adapt to the shop preview, mostly change shop list to shop name and
     * remove bulk action.
     *
     * {@inheritDoc}
     */
    protected function getColumns()
    {
        $columns = parent::getColumns();

        // No bulk column for shop previews
        $columns
            ->remove('bulk')
            ->addBefore('id_product', new EmptyColumn('bulk'))
        ;

        // Replace associated shops list with single shop details
        $columns
            ->remove('associated_shops')
            ->addBefore('image', (new ShopNameColumn('shop_name'))
            ->setName($this->trans('Store(s)', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'shop_name',
                'color_field' => 'shop_color',
            ])
        );

        // Replace active toggle column, mainly to adapt the primary key
        $columns
            ->remove('active')
            ->addBefore('position', (new ToggleColumn('active'))
            ->setName($this->trans('Status', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'active',
                'primary_field' => 'id_product',
                'route' => 'admin_products_toggle_status_for_shop',
                'route_param_name' => 'productId',
                'extra_route_params' => [
                    'shopId' => 'id_shop',
                ],
            ])
            );

        return $columns;
    }

    /**
     * Adapt the action which target multiple shop on the initial row, they here have to be shop specific.
     *
     * @return RowActionCollection
     */
    protected function getRowActions(): RowActionCollection
    {
        $rowActions = new RowActionCollection();
        $rowActions
            ->add((new LinkRowAction('edit'))
            ->setName($this->trans('Edit', [], 'Admin.Actions'))
            ->setIcon('edit')
            ->setOptions([
                'route' => 'admin_products_edit',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                // @todo: Clickable row will be handled later (if it doesn't impact the UX negatively)
                // 'clickable_row' => true,
                'extra_route_params' => [
                    'switchToShop' => 'id_shop',
                ],
            ])
            )
            ->add((new LinkRowAction('preview'))
            ->setName($this->trans('Preview', [], 'Admin.Actions'))
            ->setIcon('remove_red_eye')
            ->setOptions([
                'route' => 'admin_products_preview',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'target' => '_blank',
                'extra_route_params' => [
                    'shopId' => 'id_shop',
                ],
            ])
            )
            ->add(
                $this->buildDeleteAction(
                    'admin_products_delete_from_shop',
                    'productId',
                    'id_product',
                    'POST',
                    [
                        'shopId' => 'id_shop',
                    ],
                    [
                        'modal_options' => [
                            'title' => $this->trans('Delete from store', [], 'Admin.Global'),
                        ],
                    ],
                    $this->trans('Delete from store', [], 'Admin.Global')
                )
            )
        ;

        return $rowActions;
    }

    /**
     * Edit attributes are only used to handle the shop selection modal, not needed in a shop row.
     *
     * @return array
     */
    protected function getMultiShopEditionAttributes(): array
    {
        return [];
    }

    /**
     * We don't perform any filtering on the shop, so no need to define them.
     *
     * {@inheritDoc}
     */
    protected function getFilters()
    {
        // No filters for shop details
        return new FilterCollection();
    }
}
