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

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class responsible for providing columns, filters, actions for cart price rule list.
 */
final class DiscountGridDefinitionFactory extends AbstractGridDefinitionFactory implements FilterableGridDefinitionFactoryInterface
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'discount';

    /**
     * {@inheritdoc}
     */
    public function getFilterId(): string
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName(): string
    {
        return $this->trans('Discounts', [], 'Admin.Catalog.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollectionInterface
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_discount',
                    ])
            )
            ->add(
                (new DataColumn('id_discount'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_discount',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Discount Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('type'))
                    ->setName($this->trans('Type', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'type',
                    ])
            )
            ->add(
                (new DataColumn('code'))
                    ->setName($this->trans('Code', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'code',
                    ])
            )
            ->add(
                (new DataColumn('date_from'))
                    ->setName($this->trans('Start date', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'date_from',
                        'sortable' => true,
                    ])
            )
            ->add(
                (new DataColumn('date_to'))
                    ->setName($this->trans('Expiration date', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'date_to',
                        'sortable' => true,
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_discount',
                        'route' => 'admin_discount_toggle_status',
                        'route_param_name' => 'discountId',
                    ])
            )
            ->add(
                (new BadgeColumn('period_state'))
                    ->setName($this->trans('Period state', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'period_state',
                        'sortable' => true,
                        'badge_type' => '',
                        'badge_type_field' => 'period_state_badge_type',
                        'alignment' => 'left',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add((new LinkRowAction('edit'))
                                ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                ->setIcon('edit')
                                ->setOptions([
                                    'route' => 'admin_discount_edit',
                                    'route_param_name' => 'discountId',
                                    'route_param_field' => 'id_discount',
                                    'clickable_row' => true,
                                ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_discounts_delete',
                                    'discountId',
                                    'id_discount',
                                    Request::METHOD_DELETE
                                )
                            ),
                    ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters(): FilterCollectionInterface
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_discount', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('ID', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('id_discount')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Name', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('active', ChoiceType::class))
                    ->setTypeOptions([
                        'choices' => [
                            $this->trans('Enabled', [], 'Admin.Global') => 1,
                            $this->trans('Disabled', [], 'Admin.Global') => 0,
                        ],
                        'required' => false,
                        'placeholder' => $this->trans('All', [], 'Admin.Global'),
                        'choice_translation_domain' => false,
                    ])
                    ->setAssociatedColumn('active')
            )
            ->add((new Filter('date_from_filter', DateRangeType::class))
                ->setTypeOptions([
                    'required' => false,
                    'date_format' => 'YYYY-MM-DD HH:mm:ss',
                ])
                ->setAssociatedColumn('date_from')
            )
            ->add(
                (new Filter('date_to_filter', DateRangeType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'date_format' => 'YYYY-MM-DD HH:mm:ss',
                    ])
                    ->setAssociatedColumn('date_to')
            )
            ->add(
                (new Filter('period_filter', ChoiceType::class))
                    ->setTypeOptions([
                        'choices' => [
                            $this->trans('All', [], 'Admin.Global') => 'all',
                            $this->trans('Active', [], 'Admin.Global') => 'active',
                            $this->trans('Scheduled', [], 'Admin.Global') => 'scheduled',
                            $this->trans('Expired', [], 'Admin.Global') => 'expired',
                        ],
                        'required' => false,
                        'placeholder' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->setAssociatedColumn('period_state')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_discounts_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }
}
