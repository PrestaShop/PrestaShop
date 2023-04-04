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

use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\CartStatusesChoiceProvider;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\DeleteCartAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CartGridDefinitionFactory builds Grid definition for carts listing.
 */
class CartGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    use DeleteActionTrait;
    use BulkDeleteActionTrait;

    public const GRID_ID = 'cart';

    /**
     * @var string
     */
    private $contextDateFormat;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @var bool
     */
    private $isMultiStoreFeatureUsed;

    /**
     * @var CartStatusesChoiceProvider
     */
    private $cartStatusesChoiceProvider;

    /**
     * @var DeleteCartAccessibilityChecker
     */
    private $deleteCartAccessibilityChecker;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $contextDateFormat
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param bool $isMultiStoreFeatureUsed
     * @param CartStatusesChoiceProvider $cartStatusesChoiceProvider
     * @param DeleteCartAccessibilityChecker $deleteCartAccessibilityChecker
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        string $contextDateFormat,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        bool $isMultiStoreFeatureUsed,
        CartStatusesChoiceProvider $cartStatusesChoiceProvider,
        DeleteCartAccessibilityChecker $deleteCartAccessibilityChecker
    ) {
        parent::__construct($hookDispatcher);
        $this->contextDateFormat = $contextDateFormat;
        $this->multistoreContextChecker = $multistoreContextChecker;
        $this->isMultiStoreFeatureUsed = $isMultiStoreFeatureUsed;
        $this->cartStatusesChoiceProvider = $cartStatusesChoiceProvider;
        $this->deleteCartAccessibilityChecker = $deleteCartAccessibilityChecker;
    }

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
        return $this->trans('Carts', [], 'Admin.Global');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columnCollection = (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
            ->setOptions([
                'bulk_field' => 'id_cart',
                'disabled_field' => 'unremovable',
            ])
            )
            ->add((new DataColumn('id_cart'))
            ->setName($this->trans('ID', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'id_cart',
                'alignment' => 'center',
            ])
            )
            ->add((new DataColumn('id_order'))
            ->setName($this->trans('Order ID', [], 'Admin.Orderscustomers.Feature'))
            ->setOptions([
                'field' => 'id_order',
                'alignment' => 'center',
            ])
            )
            ->add((new BadgeColumn('status'))
            ->setName($this->trans('Status', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'status',
                'alignment' => 'center',
                'badge_type' => '',
                'badge_type_field' => 'status_badge_color',
            ])
            )
            ->add((new DataColumn('customer_name'))
            ->setName($this->trans('Customer', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'customer_name',
            ])
            )
            ->add((new BadgeColumn('cart_total'))
            ->setName($this->trans('Total', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'cart_total',
                'sortable' => false,
                'alignment' => 'center',
            ])
            )
            ->add((new DataColumn('carrier_name'))
            ->setName($this->trans('Carrier', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'carrier_name',
            ])
            )
            ->add((new DateTimeColumn('date_add'))
            ->setName($this->trans('Date', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'date_add',
                'format' => $this->contextDateFormat,
                'clickable' => true,
            ])
            )
            ->add((new DataColumn('customer_online'))
            ->setName($this->trans('Online', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'customer_online',
            ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            ->setOptions([
                'actions' => $this->getRowActions(),
            ])
            );

        if ($this->needShopNameColumn()) {
            $columnCollection->addAfter(
               'customer_online',
               (new DataColumn('shop_name'))
                   ->setName($this->trans('Shop', [], 'Admin.Global'))
                   ->setOptions([
                       'field' => 'shop_name',
                   ])
            );
        }

        return $columnCollection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add((new LinkGridAction('export'))
            ->setName($this->trans('Export', [], 'Admin.Actions'))
            ->setIcon('cloud_download')
            ->setOptions([
                'route' => 'admin_carts_export',
            ])
            )
            ->add((new SimpleGridAction('common_refresh_list'))
            ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
            ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
            ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
            ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
            ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
            ->setIcon('storage')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_cart', TextType::class))
                    ->setAssociatedColumn('id_cart')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('id_order', TextType::class))
                    ->setAssociatedColumn('id_order')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('status', ChoiceType::class))
                    ->setAssociatedColumn('status')
                    ->setTypeOptions([
                        'choices' => $this->cartStatusesChoiceProvider->getChoices(),
                        'expanded' => false,
                        'multiple' => false,
                        'required' => false,
                        'choice_translation_domain' => false,
                    ])
            )
            ->add(
                (new Filter('customer_name', TextType::class))
                    ->setAssociatedColumn('customer_name')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('carrier_name', TextType::class))
                    ->setAssociatedColumn('carrier_name')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                 (new Filter('date_add', DateRangeType::class))
                     ->setAssociatedColumn('date_add')
                     ->setTypeOptions([
                         'required' => false,
                     ])
            )
            ->add(
                (new Filter('customer_online', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('customer_online')
                    ->setTypeOptions([
                        'required' => false,
                    ])
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_carts_index',
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('admin_carts_bulk_delete')
            );
    }

    /**
     * Add row actions in grid.
     *
     * @return RowActionCollectionInterface
     */
    protected function getRowActions(): RowActionCollectionInterface
    {
        return (new RowActionCollection())
            ->add(
                (new LinkRowAction('view'))
                    ->setName($this->trans('View', [], 'Admin.Actions'))
                    ->setIcon('zoom_in')
                    ->setOptions([
                        'route' => 'admin_carts_view',
                        'route_param_name' => 'cartId',
                        'route_param_field' => 'id_cart',
                        'clickable_row' => true,
                    ])
            )
            ->add(
                $this->buildDeleteAction(
                    'admin_carts_delete',
                    'cartId',
                    'id_cart',
                    Request::METHOD_DELETE,
                    [],
                    [
                        'accessibility_checker' => $this->deleteCartAccessibilityChecker,
                    ]
                )
            );
    }

    /**
     * Function aim to define if we need to add Shop Name column in grid.
     * (Only if we are on Multistore mode and All ou Group context)
     *
     * @return bool
     */
    private function needShopNameColumn(): bool
    {
        return $this->isMultiStoreFeatureUsed && !$this->multistoreContextChecker->isSingleShopContext();
    }
}
