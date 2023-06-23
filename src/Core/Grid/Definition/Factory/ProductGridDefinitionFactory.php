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
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\AjaxBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\EmptyColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ImageColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Product\ShopListColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopConstraintContextInterface;
use PrestaShopBundle\Form\Admin\Type\IntegerMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\NumberMinMaxFilterType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\ShopSelectorType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Defines products grid name, its columns, actions, bulk actions and filters.
 */
class ProductGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'product';

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * @var ShopConstraintContextInterface
     */
    private $shopConstraintContext;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var AccessibilityCheckerInterface
     */
    private $singleShopChecker;

    /**
     * @var AccessibilityCheckerInterface
     */
    private $multipleShopsChecker;

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        ConfigurationInterface $configuration,
        FeatureInterface $multistoreFeature,
        ShopConstraintContextInterface $shopConstraintContext,
        FormFactoryInterface $formFactory,
        AccessibilityCheckerInterface $singleShopChecker,
        AccessibilityCheckerInterface $multipleShopsChecker
    ) {
        parent::__construct($hookDispatcher);
        $this->configuration = $configuration;
        $this->multistoreFeature = $multistoreFeature;
        $this->shopConstraintContext = $shopConstraintContext;
        $this->formFactory = $formFactory;
        $this->singleShopChecker = $singleShopChecker;
        $this->multipleShopsChecker = $multipleShopsChecker;
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
        $editAttributes = $this->getMultiShopEditionAttributes();

        $shopId = null;
        if ($this->shopConstraintContext->getShopConstraint()->getShopId()) {
            $shopId = $this->shopConstraintContext->getShopConstraint()->getShopId()->getValue();
        }

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
                        'route' => 'admin_products_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'attr' => $editAttributes,
                    ])
            )
            ->add(
                (new LinkColumn('reference'))
                    ->setName($this->trans('Reference', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'reference',
                        'route' => 'admin_products_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'route_fragment' => 'tab-product_details-tab',
                        'attr' => $editAttributes,
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
                        'route' => 'admin_products_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'route_fragment' => 'tab-product_pricing-tab',
                        'attr' => $editAttributes,
                    ])
            )
            ->add(
                (new LinkColumn('price_tax_included'))
                    ->setName($this->trans('Price (tax incl.)', [], 'Admin.Catalog.Feature'))
                    ->setOptions([
                        'field' => 'price_tax_included',
                        'sortable' => false,
                        'route' => 'admin_products_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'route_fragment' => 'tab-product_pricing-tab',
                        'attr' => $editAttributes,
                    ])
            )
        ;

        if (!empty($shopId)) {
            $columns
                ->add(
                    (new ToggleColumn('active'))
                        ->setName($this->trans('Status', [], 'Admin.Global'))
                        ->setOptions([
                            'field' => 'active',
                            'primary_field' => 'id_product',
                            'route' => 'admin_products_toggle_status_for_shop',
                            'route_param_name' => 'productId',
                            'extra_route_params' => [
                                'shopId' => $shopId,
                            ],
                        ])
                )
            ;
        } else {
            $columns
                ->add(
                    (new EmptyColumn('active'))
                        ->setName($this->trans('Status', [], 'Admin.Global'))
                        ->setOptions([
                            'empty_value' => '-',
                        ])
                )
            ;
        }

        $columns
            ->add(
                (new PositionColumn('position'))
                    ->setName($this->trans('Position', [], 'Admin.Global'))
                    ->setOptions([
                        'id_field' => 'id_product',
                        'position_field' => 'position',
                        'update_method' => 'POST',
                        'update_route' => 'admin_products_update_position',
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
                        'route' => 'admin_products_edit',
                        'route_param_name' => 'productId',
                        'route_param_field' => 'id_product',
                        'route_fragment' => 'tab-product_stock-tab',
                        'attr' => $editAttributes,
                    ])
            );
        }

        if ($this->shopConstraintContext->getShopConstraint()->forAllShops() || $this->shopConstraintContext->getShopConstraint()->getShopGroupId()) {
            $columns->addBefore('image', (new ShopListColumn('associated_shops'))
                ->setName($this->trans('Store(s)', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'associated_shops',
                    'ids_field' => 'associated_shops_ids',
                    'product_id_field' => 'id_product',
                    'max_displayed_characters' => 35,
                    'shop_group_id' => $this->shopConstraintContext->getShopConstraint()->getShopGroupId() ?
                        $this->shopConstraintContext->getShopConstraint()->getShopGroupId()->getValue() : null,
                ])
            );
        }

        return $columns;
    }

    /**
     * {@inheritDoc}
     */
    protected function getRowActions(): RowActionCollection
    {
        if ($this->shopConstraintContext->getShopConstraint()->forAllShops() || $this->shopConstraintContext->getShopConstraint()->getShopGroupId()) {
            return $this->getSingleMultiShopsRowActions();
        }

        return $this->getSingleShopRowActions();
    }

    protected function getSingleShopRowActions(): RowActionCollection
    {
        // By default, use the default value from the trait
        $deleteLabel = null;
        $duplicateLabel = $this->trans('Duplicate', [], 'Admin.Actions');
        $shopId = $this->shopConstraintContext->getShopConstraint()->getShopId()->getValue();
        if ($this->multistoreFeature->isActive()) {
            $deleteLabel = $this->trans('Delete from store', [], 'Admin.Actions');
            $duplicateLabel = $this->trans('Duplicate for current store', [], 'Admin.Actions');
        }

        $rowActions = new RowActionCollection();
        $rowActions
            ->add((new LinkRowAction('edit'))
            ->setName($this->trans('Edit', [], 'Admin.Actions'))
            ->setIcon('edit')
            ->setOptions([
                'route' => 'admin_products_edit',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'clickable_row' => true,
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
                'accessibility_checker' => $this->singleShopChecker,
            ])
            )
            ->add((new SubmitRowAction('duplicate'))
            ->setName($duplicateLabel)
            ->setIcon('content_copy')
            ->setOptions([
                'method' => 'POST',
                'route' => 'admin_products_duplicate_shop',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'extra_route_params' => [
                    'shopId' => $shopId,
                ],
                'modal_options' => new ModalOptions([
                    'title' => $this->trans('Duplicate product', [], 'Admin.Actions'),
                    'confirm_button_label' => $duplicateLabel,
                    'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                ]),
            ])
            )
            ->add(
                $this->buildDeleteAction(
                    'admin_products_delete_from_shop',
                    'productId',
                    'id_product',
                    'POST',
                    ['shopId' => $shopId],
                    [],
                    $deleteLabel
                )
            )
        ;

        return $rowActions;
    }

    protected function getSingleMultiShopsRowActions(): RowActionCollection
    {
        // We use only one variable for extra params because they happen to match for all use cases, they may need to be split in the future
        $extraRouteParams = [];
        if ($this->shopConstraintContext->getShopConstraint()->getShopGroupId()) {
            $deleteRouteName = 'admin_products_delete_from_shop_group';
            $duplicateRouteName = 'admin_products_duplicate_shop_group';
            $enableRouteName = 'admin_products_enable_for_shop_group';
            $disableRouteName = 'admin_products_disable_for_shop_group';
            $extraRouteParams = [
                'shopGroupId' => $this->shopConstraintContext->getShopConstraint()->getShopGroupId()->getValue(),
            ];

            $deleteLabel = $this->trans('Delete from group', [], 'Admin.Actions');
            $enableLabel = $this->trans('Enable for group', [], 'Admin.Actions');
            $disableLabel = $this->trans('Disable for group', [], 'Admin.Actions');
            $duplicateLabel = $this->trans('Duplicate group', [], 'Admin.Actions');
        } else {
            $deleteRouteName = 'admin_products_delete_from_all_shops';
            $duplicateRouteName = 'admin_products_duplicate_all_shops';
            $enableRouteName = 'admin_products_enable_for_all_shops';
            $disableRouteName = 'admin_products_disable_for_all_shops';

            $deleteLabel = $this->trans('Delete from all stores', [], 'Admin.Actions');
            $enableLabel = $this->trans('Enable on all stores', [], 'Admin.Actions');
            $disableLabel = $this->trans('Disable on all stores', [], 'Admin.Actions');
            $duplicateLabel = $this->trans('Duplicate all stores', [], 'Admin.Actions');
        }

        $rowActions = new RowActionCollection();
        $rowActions
            ->add((new LinkRowAction('single_shop_edit'))
            ->setName($this->trans('Edit', [], 'Admin.Actions'))
            ->setIcon('edit')
            ->setOptions([
                'route' => 'admin_products_edit',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'clickable_row' => true,
                // Only present when product has strictly one shop
                'accessibility_checker' => $this->singleShopChecker,
                // We force the shop switching in this case
                'extra_route_params' => [
                    'switchToShop' => 'id_shop_default',
                ],
            ])
            )
            ->add((new LinkRowAction('multi_shops_edit'))
            ->setName($this->trans('Edit', [], 'Admin.Actions'))
            ->setIcon('edit')
            ->setOptions([
                'route' => 'admin_products_edit',
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'clickable_row' => true,
                'attr' => $this->getMultiShopEditionAttributes(),
                // Only present when product has more than one shop
                'accessibility_checker' => $this->multipleShopsChecker,
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
                'accessibility_checker' => $this->singleShopChecker,
            ])
            )
            ->add((new SubmitRowAction('duplicate'))
            ->setName($duplicateLabel)
            ->setIcon('content_copy')
            ->setOptions([
                'method' => 'POST',
                'route' => $duplicateRouteName,
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'extra_route_params' => $extraRouteParams,
                'modal_options' => new ModalOptions([
                    'title' => $this->trans('Duplicate product', [], 'Admin.Actions'),
                    'confirm_button_label' => $duplicateLabel,
                    'close_button_label' => $this->trans('Cancel', [], 'Admin.Actions'),
                ]),
            ])
            )
            ->add(
                $this->buildDeleteAction(
                    $deleteRouteName,
                    'productId',
                    'id_product',
                    'POST',
                    $extraRouteParams,
                    [],
                    $deleteLabel
                )
            )
            // Toggle column is disabled when product is associated to more than one shop, so enable/disable actions are handled via the dropdown actions
            ->add((new LinkRowAction('enable'))
            ->setName($enableLabel)
            ->setIcon('radio_button_checked')
            ->setOptions([
                'route' => $enableRouteName,
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'extra_route_params' => $extraRouteParams,
            ])
            )
            ->add((new LinkRowAction('disable'))
            ->setName($disableLabel)
            ->setIcon('radio_button_unchecked')
            ->setOptions([
                'route' => $disableRouteName,
                'route_param_name' => 'productId',
                'route_param_field' => 'id_product',
                'extra_route_params' => $extraRouteParams,
            ])
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
                        'redirect_route' => 'admin_products_index',
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
        if ($this->shopConstraintContext->getShopConstraint()->getShopId()) {
            $bulkEnableRoute = 'admin_products_bulk_enable_shop';
            $bulkDisableRoute = 'admin_products_bulk_disable_shop';
            $bulkDuplicateRoute = 'admin_products_bulk_duplicate_shop';
            $bulkDeleteRoute = 'admin_products_bulk_delete_from_shop';
            $routeParams = [
                'shopId' => $this->shopConstraintContext->getShopConstraint()->getShopId()->getValue(),
            ];
            if ($this->multistoreFeature->isActive()) {
                $bulkEnableLabel = $this->trans('Activate selection for current store', [], 'Admin.Actions');
                $bulkDisableLabel = $this->trans('Deactivate selection for current store', [], 'Admin.Actions');
                $bulkDuplicateLabel = $this->trans('Duplicate selection for current store', [], 'Admin.Actions');
                $bulkDeleteLabel = $this->trans('Delete selection for current store', [], 'Admin.Actions');
            } else {
                $bulkEnableLabel = $this->trans('Activate selection', [], 'Admin.Actions');
                $bulkDisableLabel = $this->trans('Deactivate selection', [], 'Admin.Actions');
                $bulkDuplicateLabel = $this->trans('Duplicate selection', [], 'Admin.Actions');
                $bulkDeleteLabel = $this->trans('Delete selection', [], 'Admin.Actions');
            }
        } elseif ($this->shopConstraintContext->getShopConstraint()->getShopGroupId()) {
            $bulkEnableRoute = 'admin_products_bulk_enable_shop_group';
            $bulkDisableRoute = 'admin_products_bulk_disable_shop_group';
            $bulkDuplicateRoute = 'admin_products_bulk_duplicate_shop_group';
            $bulkDeleteRoute = 'admin_products_bulk_delete_from_shop_group';
            $routeParams = [
                'shopGroupId' => $this->shopConstraintContext->getShopConstraint()->getShopGroupId()->getValue(),
            ];
            $bulkEnableLabel = $this->trans('Activate selection for group', [], 'Admin.Actions');
            $bulkDisableLabel = $this->trans('Deactivate selection for group', [], 'Admin.Actions');
            $bulkDuplicateLabel = $this->trans('Duplicate selection for group', [], 'Admin.Actions');
            $bulkDeleteLabel = $this->trans('Delete selection for group', [], 'Admin.Actions');
        } else {
            $bulkEnableRoute = 'admin_products_bulk_enable_all_shops';
            $bulkDisableRoute = 'admin_products_bulk_disable_all_shops';
            $bulkDuplicateRoute = 'admin_products_bulk_duplicate_all_shops';
            $bulkDeleteRoute = 'admin_products_bulk_delete_from_all_shops';
            $routeParams = [];
            $bulkEnableLabel = $this->trans('Activate selection for all stores', [], 'Admin.Actions');
            $bulkDisableLabel = $this->trans('Deactivate selection for all stores', [], 'Admin.Actions');
            $bulkDuplicateLabel = $this->trans('Duplicate selection for associated stores', [], 'Admin.Actions');
            $bulkDeleteLabel = $this->trans('Delete selection for all stores', [], 'Admin.Actions');
        }

        return (new BulkActionCollection())
            ->add($this->buildAjaxBulkAction(
                'enable_selection_ajax',
                $bulkEnableRoute,
                $bulkEnableLabel,
                $this->trans('Activating %total% products', [], 'Admin.Actions'),
                $this->trans('Activating %done% / %total% products', [], 'Admin.Actions'),
                'radio_button_checked',
                $routeParams
            ))
            ->add($this->buildAjaxBulkAction(
                'disable_selection_ajax',
                $bulkDisableRoute,
                $bulkDisableLabel,
                $this->trans('Deactivating %total% products', [], 'Admin.Actions'),
                $this->trans('Deactivating %done% / %total% products', [], 'Admin.Actions'),
                'radio_button_unchecked',
                $routeParams
            ))
            ->add($this->buildAjaxBulkAction(
                'bulk_duplicate_ajax',
                $bulkDuplicateRoute,
                $bulkDuplicateLabel,
                $this->trans('Duplicating %total% products', [], 'Admin.Actions'),
                $this->trans('Duplicating %done% / %total% products', [], 'Admin.Actions'),
                'content_copy',
                $routeParams
            ))
            ->add($this->buildAjaxBulkAction(
                'bulk_delete_ajax',
                $bulkDeleteRoute,
                $bulkDeleteLabel,
                $this->trans('Deleting %total% products', [], 'Admin.Actions'),
                $this->trans('Deleting %done% / %total% products', [], 'Admin.Actions'),
                'delete',
                $routeParams
            ))
        ;
    }

    /**
     * @return array<string, string>
     */
    protected function getMultiShopEditionAttributes(): array
    {
        if ($this->shopConstraintContext->getShopConstraint()->forAllShops() || $this->shopConstraintContext->getShopConstraint()->getShopGroupId()) {
            return [
                'class' => 'multi-shop-edit-product',
                'data-modal-title' => $this->trans('Select a store', [], 'Admin.Catalog.Feature'),
                'data-shop-selector' => $this->formFactory->create(ShopSelectorType::class),
            ];
        }

        return [];
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
