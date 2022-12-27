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

use PrestaShop\PrestaShop\Core\Grid\Column\Type\EmptyColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Product\ShopNameColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;

/**
 * This grid definition is based on the product list one, it is used to display the details of each shop
 * for a specific product, by extending the initial definition we keep the same column and actions in every details.
 */
class ProductShopsGridDefinitionFactory extends ProductGridDefinitionFactory
{
    protected function getColumns()
    {
        $columns = parent::getColumns();

        // No bulk column for shop details
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

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        // No filters for shop details
        return new FilterCollection();
    }
}
