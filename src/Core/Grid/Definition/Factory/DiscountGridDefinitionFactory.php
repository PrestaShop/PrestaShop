<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\ViewOptionsCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DiscountUsageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountSearchAndResetType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountTypeChoiceType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\FilterLinkFilterType;
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
                    ->setName($this->trans('Discount name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new BadgeColumn('discount_type'))
                    ->setName($this->trans('Type', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'discount_type_name',
                        'alignment' => 'left',
                        'badge_type' => 'light-info',
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
                (new DiscountUsageColumn('usage'))
                    ->setName($this->trans('Usage', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'quantity_used_field' => 'quantity_used',
                        'total_quantity_field' => 'total_quantity',
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
                    ->setName($this->trans('End date', [], 'Admin.Catalog.Feature'))
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
                            ->add((new SubmitRowAction('duplicate'))
                                ->setName($this->trans('Duplicate', [], 'Admin.Actions'))
                                ->setIcon('content_copy')
                                ->setOptions([
                                    'method' => 'POST',
                                    'route' => 'admin_discounts_duplicate',
                                    'route_param_name' => 'discountId',
                                    'route_param_field' => 'id_discount',
                                    'confirm_message' => $this->trans('Remember to properly edit all information after duplicating.', [], 'Admin.Catalog.Notification'),
                                    'modal_options' => new ModalOptions([
                                        'title' => $this->trans('Duplicate discount', [], 'Admin.Actions'),
                                        'confirm_button_label' => $this->trans('Duplicate', [], 'Admin.Actions'),
                                        'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                                    ]),
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
                (new Filter('code', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Code', [], 'Admin.Global'),
                        ],
                    ])
                    ->setAssociatedColumn('code')
            )
            ->add(
                (new Filter('discount_type', DiscountTypeChoiceType::class))
                    ->setAssociatedColumn('discount_type')
            )
            ->add(
                (new Filter('active', ChoiceType::class))
                    ->setAssociatedColumn('active')
                    ->setTypeOptions([
                        'choices' => [
                            $this->trans('Enabled', [], 'Admin.Global') => 1,
                            $this->trans('Disabled', [], 'Admin.Global') => 0,
                        ],
                        'required' => false,
                        'placeholder' => $this->trans('All', [], 'Admin.Global'),
                        'choice_translation_domain' => false,
                    ])
            )
            ->add(
                (new Filter('period_filter', FilterLinkFilterType::class))
                    ->setTypeOptions([
                        'filter_field_name' => 'period_filter',
                        'filter_field_selector' => '[data-role="period_filter-filter-field"]',
                        'default_value' => DiscountSettings::PERIOD_FILTER_ALL,
                        'filter_options' => [
                            DiscountSettings::PERIOD_FILTER_ALL => $this->trans('All', [], 'Admin.Global'),
                            DiscountSettings::PERIOD_FILTER_ACTIVE => $this->trans('Active', [], 'Admin.Catalog.Feature'),
                            DiscountSettings::PERIOD_FILTER_SCHEDULED => $this->trans('Scheduled', [], 'Admin.Catalog.Feature'),
                            DiscountSettings::PERIOD_FILTER_EXPIRED => $this->trans('Expired', [], 'Admin.Catalog.Feature'),
                        ],
                        'attr' => [
                            'class' => 'js-period-filter-field',
                            'data-role' => 'period_filter-filter-field',
                            'data-filter-field-name' => 'period_filter',
                        ],
                        'data' => DiscountSettings::PERIOD_FILTER_ALL,
                        'empty_data' => DiscountSettings::PERIOD_FILTER_ALL,
                    ])
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
                (new Filter('actions', DiscountSearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_discounts_reset_grid',
                        'redirect_route' => 'admin_discounts_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_discount_bulk_enable_status',
                    ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_discount_bulk_disable_status',
                    ])
            )
            ->add(
                $this->buildBulkDeleteAction('admin_discount_bulk_delete')
            );
    }

    /**
     * {@inheritDoc}
     */
    protected function getViewOptions()
    {
        return (new ViewOptionsCollection())->add('empty_state_without_headers', false);
    }
}
