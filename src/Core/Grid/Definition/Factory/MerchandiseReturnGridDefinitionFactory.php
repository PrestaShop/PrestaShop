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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\ColorColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class MerchandiseReturnGridDefinitionFactory builds grid definition for merchandise returns grid.
 */
final class MerchandiseReturnGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    const GRID_ID = 'merchandise_return';

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
        return $this->trans('Merchandise Returns', [], 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DataColumn('id_order_return'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_order_return',
                    ])
            )
            ->add(
                (new DataColumn('id_order'))
                    ->setName($this->trans('Order ID', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'id_order',
                    ])
            )
            ->add(
                (new ColorColumn('status'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'status',
                        'color_field' => 'color',
                    ])
            )
            ->add(
                (new DateTimeColumn('date_add'))
                    ->setName($this->trans('Date issued', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'date_add',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
            )
        ;

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_order_return', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('id_order_return')
            )
            ->add((new Filter('id_order', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search order ID', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('id_order')
            )
            ->add((new Filter('status', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('status')
            )
            ->add((new Filter('date_add', DateRangeType::class))
                ->setTypeOptions([
                    'required' => false,
                    'date_format' => 'YYYY-MM-DD',
                ])
                ->setAssociatedColumn('date_add')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'reset_route' => 'admin_common_reset_search_by_filter_id',
                    'reset_route_params' => [
                        'filterId' => self::GRID_ID,
                    ],
                    'redirect_route' => 'admin_merchandise_returns_index',
                ])
                ->setAssociatedColumn('actions')
            )
        ;
    }
}
