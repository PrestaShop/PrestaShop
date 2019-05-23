<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\OrderPriceColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\ColorColumn;

/**
 * Creates definition for Orders grid
 */
final class OrderGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    const GRID_ID = 'order';

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
        return $this->trans('Orders', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new DataColumn('id_order'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_order',
                ])
            )
            ->add((new DataColumn('reference'))
                ->setName($this->trans('Reference', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'reference',
                ])
            )
            ->add((new BooleanColumn('new'))
                ->setName($this->trans('New client', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'new',
                    'true_name' => $this->trans('Yes', [], 'Admin.Global'),
                    'false_name' => $this->trans('No', [], 'Admin.Global'),
                ])
            )
            ->add((new DataColumn('customer'))
                ->setName($this->trans('Customer', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'customer',
                ])
            )
            ->add((new OrderPriceColumn('total_paid_tax_incl'))
                ->setName($this->trans('Total', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'total_paid_tax_incl',
                    'is_paid_field' => 'paid',
                ])
            )
            ->add((new ColorColumn('osname'))
                ->setName($this->trans('Status', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'osname',
                    'color_field' => 'color',
                ])
            )
        ;
    }
}
