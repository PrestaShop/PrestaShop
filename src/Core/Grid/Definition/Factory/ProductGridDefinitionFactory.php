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
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
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
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\IntegerMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\NumberMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Defines products grid name, its columns, actions, bulk actions and filters.
 */
final class ProductGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

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
                        'alt_field' => 'legend',
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
                (new LinkColumn('reference'))
                    ->setName($this->trans('Reference', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'reference',
                        'route' => 'admin_products_v2_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'route_fragment' => 'tab-product_specifications-tab',
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
                (new LinkColumn('final_price_tax_excluded'))
                    ->setName($this->trans('Price (tax excl.)', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'price_tax_excluded',
                        'route' => 'admin_products_v2_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'route_fragment' => 'tab-product_pricing-tab',
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
                        'route_fragment' => 'tab-product_pricing-tab',
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
                        'id_field' => 'id_product',
                        'position_field' => 'position',
                        'update_method' => 'POST',
                        'update_route' => 'admin_products_v2_update_position',
                        'record_route_params' => [
                            'id_category' => 'id_category',
                        ],
                        // Only display this column when list is filtered by category
                        'required_filter' => 'id_category',
                        // Positions are already 1-indexed so no need to offset the display
                        // @see prestashop.core.grid.product.position_definition where $firstPosition is already set to 1
                        'display_offset' => 0,
                    ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            ->setOptions([
                'actions' => $this->getRowActions(),
            ])
            )
        ;

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
                        'route_fragment' => 'tab-product_stock-tab',
                    ])
            );
        }

        return $columns;
    }

    protected function getRowActions(): RowActionCollection
    {
        $rowActions = new RowActionCollection();
        $rowActions
            ->add((new LinkRowAction('edit'))
            ->setName($this->trans('Edit', [], 'Admin.Actions'))
            ->setIcon('edit')
            ->setOptions([
                'route' => 'admin_products_v2_edit',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'clickable_row' => true,
            ])
            )
            ->add((new LinkRowAction('preview'))
            ->setName($this->trans('Preview', [], 'Admin.Actions'))
            ->setIcon('remove_red_eye')
            ->setOptions([
                'route' => 'admin_products_v2_preview',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'target' => '_blank',
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
                'modal_options' => new ModalOptions([
                    'title' => $this->trans('Duplicate product', [], 'Admin.Actions'),
                    'confirm_button_label' => $this->trans('Duplicate', [], 'Admin.Actions'),
                    'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                ]),
            ])
            )
            ->add(
                $this->buildDeleteAction(
                    'admin_products_v2_delete',
                    'productId',
                    'id_product'
                )
            )
        ;

        return $rowActions;
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
                (new Filter('final_price_tax_excluded', NumberMinMaxFilterType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('final_price_tax_excluded')
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
                        'reset_route' => 'admin_products_reset_grid_search',
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
                            // Ignore default zero value to use negative values
                            'min_field_options' => [
                                'attr' => [
                                    'min' => false,
                                ],
                            ],
                            'max_field_options' => [
                                'attr' => [
                                    'min' => false,
                                ],
                            ],
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
            ->add($this->buildAjaxBulkAction(
                'enable_selection_ajax',
                'admin_products_v2_bulk_enable',
                $this->trans('Activate selection', [], 'Admin.Actions'),
                $this->trans('Activating %total% products', [], 'Admin.Actions'),
                $this->trans('Activating %done% / %total% products', [], 'Admin.Actions'),
                'radio_button_checked',
                ['productStatus' => true]
            ))
            ->add($this->buildAjaxBulkAction(
                'disable_selection_ajax',
                'admin_products_v2_bulk_disable',
                $this->trans('Deactivate selection', [], 'Admin.Actions'),
                $this->trans('Deactivating %total% products', [], 'Admin.Actions'),
                $this->trans('Deactivating %done% / %total% products', [], 'Admin.Actions'),
                'radio_button_unchecked',
                ['productStatus' => false]
            ))
            ->add($this->buildAjaxBulkAction(
                'bulk_duplicate_ajax',
                'admin_products_v2_bulk_duplicate',
                $this->trans('Duplicate selection', [], 'Admin.Actions'),
                $this->trans('Duplicating %total% products', [], 'Admin.Actions'),
                $this->trans('Duplicating %done% / %total% products', [], 'Admin.Actions'),
                'content_copy'
            ))
            ->add($this->buildAjaxBulkAction(
                'bulk_delete_ajax',
                'admin_products_v2_bulk_delete',
                $this->trans('Delete selection', [], 'Admin.Actions'),
                $this->trans('Deleting %total% products', [], 'Admin.Actions'),
                $this->trans('Deleting %done% / %total% products', [], 'Admin.Actions'),
                'delete'
            ))
        ;
    }

    protected function buildAjaxBulkAction(
        string $actionId,
        string $ajaxRoute,
        string $actionLabel,
        string $progressTitle,
        string $progressMessage,
        string $icon = '',
        array $routeParams = []
    ): AjaxBulkAction {
        $ajaxBulkAction = new AjaxBulkAction($actionId);
        $ajaxBulkAction
            ->setName($actionLabel)
            ->setOptions([
                'ajax_route' => $ajaxRoute,
                'route_params' => $routeParams,
                'request_param_name' => 'product_bulk',
                'confirm_bulk_action' => true,
                'modal_confirm_title' => $actionLabel,
                'modal_cancel' => $this->trans('Cancel', [], 'Admin.Actions'),
                'modal_progress_title' => $progressTitle,
                'modal_progress_message' => $progressMessage,
                'modal_close' => $this->trans('Close', [], 'Admin.Actions'),
                'modal_stop_processing' => $this->trans('Stop processing', [], 'Admin.Actions'),
                'modal_errors_message' => $this->trans('%error_count% errors occurred. You can download the logs for future reference.', [], 'Admin.Actions'),
                'modal_back_to_processing' => $this->trans('Back to processing', [], 'Admin.Actions'),
                'modal_download_error_log' => $this->trans('Download error log', [], 'Admin.Actions'),
                'modal_view_error_log' => $this->trans('View %error_count% error logs', [], 'Admin.Actions'),
                'modal_error_title' => $this->trans('Error log', [], 'Admin.Catalog.Feature'),
            ])
            ->setIcon($icon)
        ;

        return $ajaxBulkAction;
    }
}
