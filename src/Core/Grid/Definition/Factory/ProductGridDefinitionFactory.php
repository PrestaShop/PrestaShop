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

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\AjaxBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\HiddenFilter;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\IntegerMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\NumberMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Defines products grid name, its columns, actions, bulk actions and filters.
 */
final class ProductGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;

    public const GRID_ID = 'product';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        ConfigurationInterface $configuration
    ) {
        parent::__construct($hookDispatcher);

        $this->configuration = $configuration;
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
        return $this->trans('Products', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_product',
                    ])
            )
            ->add(
                (new DataColumn('id_product'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_product',
                    ])
            )
            ->add(
                (new ImageColumn('image'))
                    ->setName($this->trans('Image', [], 'Admin.Global'))
                    ->setOptions([
                        'src_field' => 'image',
                    ])
            )
            ->add(
                (new LinkColumn('name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                        'route' => 'admin_products_v2_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
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
                (new DataColumn('category'))
                    ->setName($this->trans('Category', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'category',
                    ])
            )
            ->add(
                (new LinkColumn('price_tax_excluded'))
                    ->setName($this->trans('Price (tax excl.)', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'price_tax_excluded',
                        'route' => 'admin_products_v2_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'fragment' => 'tab-pricing-tab',
                    ])
            )
            ->add(
                (new LinkColumn('price_tax_included'))
                    ->setName($this->trans('Price (tax incl.)', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'price_tax_included',
                        'sortable' => false,
                        'route' => 'admin_products_v2_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'fragment' => 'tab-pricing-tab',
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Status', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_product',
                        'route' => 'admin_products_v2_toggle_status',
                        'route_param_name' => 'productId',
                    ])
            )
            ->add(
                (new PositionColumn('position'))
                    ->setName($this->trans('Position', [], 'Admin.Global'))
                    ->setOptions([
                        'increment_position' => false,
                        'id_field' => 'id_product',
                        'position_field' => 'position',
                        'update_method' => 'POST',
                        'update_route' => 'admin_products_v2_update_position',
                        'record_route_params' => [
                            'id_category' => 'id_category',
                        ],
                    ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            ->setOptions([
                'actions' => (new RowActionCollection())
                    ->add((new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_products_v2_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                    ])
                    )
                    ->add((new SubmitRowAction('preview'))
                    ->setName($this->trans('Preview', [], 'Admin.Actions'))
                    ->setIcon('remove_red_eye')
                    ->setOptions([
                        'method' => 'POST',
                        'route' => 'admin_products_v2_preview',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                    ])
                    )
                    ->add((new SubmitRowAction('duplicate'))
                    ->setName($this->trans('Duplicate', [], 'Admin.Actions'))
                    ->setIcon('content_copy')
                    ->setOptions([
                        'method' => 'POST',
                        'route' => 'admin_products_v2_duplicate',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'confirm_message' => $this->trans(
                            'Duplicate selected item?',
                            [],
                            'Admin.Notifications.Warning'
                        ),
                    ])
                    )
                    ->add((new SubmitRowAction('delete'))
                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                    ->setIcon('delete')
                    ->setOptions([
                        'method' => 'DELETE',
                        'route' => 'admin_products_v2_delete',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'confirm_message' => $this->trans(
                            'Delete selected item?',
                            [],
                            'Admin.Notifications.Warning'
                        ),
                    ])
                    ),
            ])
            );
        if ($this->configuration->get('PS_STOCK_MANAGEMENT')) {
            $columns->addAfter(
                'price_tax_included',
                (new LinkColumn('quantity'))
                    ->setName($this->trans('Quantity', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'quantity',
                        'route' => 'admin_products_v2_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'fragment' => 'tab-stock-tab',
                    ])
            );
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = (new FilterCollection())
            ->add(
                (new Filter('id_product', IntegerMinMaxFilterType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_product')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search name', [], 'Admin.Catalog.Help'),
                        ],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('reference', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search reference', [], 'Admin.Catalog.Help'),
                        ],
                    ])
                    ->setAssociatedColumn('reference')
            )
            ->add(
                (new HiddenFilter('id_category'))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_category')
            )
            ->add(
                (new Filter('category', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search category', [], 'Admin.Catalog.Help'),
                        ],
                    ])
                    ->setAssociatedColumn('category')
            )
            ->add(
                (new Filter('price_tax_excluded', NumberMinMaxFilterType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('price_tax_excluded')
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
            )
            ->add((new Filter('position', TextType::class))
            ->setAssociatedColumn('position')
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search position', [], 'Admin.Actions'),
                ],
            ])
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_products_v2_index',
                    ])
                    ->setAssociatedColumn('actions')
            );

        if ($this->configuration->get('PS_STOCK_MANAGEMENT')) {
            $filters
                ->add(
                    (new Filter('quantity', IntegerMinMaxFilterType::class))
                        ->setTypeOptions([
                            'required' => false,
                        ])
                        ->setAssociatedColumn('quantity')
                )
            ;
        }

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(
                (new LinkGridAction('import'))
                    ->setName($this->trans('Import', [], 'Admin.Actions'))
                    ->setIcon('cloud_upload')
                    ->setOptions([
                        'route' => 'admin_import',
                        'route_params' => [
                            'import_type' => 'products',
                        ],
                    ])
            )
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

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new AjaxBulkAction('enable_selection_ajax'))
                    ->setName($this->trans('Activate selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'ajax_route' => 'admin_products_v2_activate_ajax',
                        'modal_title' => $this->trans('Activating %d products', [], 'Admin.Actions'),
                        'modal_close_button_label' => $this->trans('Close', [], 'Admin.Actions'),
                        'modal_progress_title' => $this->trans('Activating...', [], 'Admin.Actions'),
                        'modal_failure_title' => $this->trans('Failed to activate:', [], 'Admin.Actions'),
                        'modal_description' => $this->trans('Product activation is in progress, please don\'t close the window', [], 'Admin.Actions')
                    ])
            )
            ->add(
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Activate selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_products_v2_bulk_enable',
                    ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Deactivate selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_products_v2_bulk_disable',
                    ])
            )
            ->add(
                (new SubmitBulkAction('duplicate_selection'))
                    ->setName($this->trans('Duplicate selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_products_v2_bulk_duplicate',
                    ])
            )
            ->add(
                $this->buildBulkDeleteAction(
                    'admin_products_v2_bulk_delete'
                )
            );
    }
}
