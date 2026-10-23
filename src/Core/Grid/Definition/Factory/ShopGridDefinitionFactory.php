<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AddShopUrlAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\DeleteShopAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollectionInterface;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ShopGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'shop';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private readonly DeleteShopAccessibilityChecker $deleteAccessibilityChecker,
        private readonly AddShopUrlAccessibilityChecker $addShopUrlAccessibilityChecker,
    ) {
        parent::__construct($hookDispatcher);
    }

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Stores', [], 'Admin.Navigation.Menu');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add(
                (new DataColumn('id_shop'))
                    ->setName($this->trans('Shop ID', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions(['field' => 'id_shop'])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Store name', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions(['field' => 'name'])
            )
            ->add(
                (new DataColumn('shop_group_name'))
                    ->setName($this->trans('Shop group', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions(['field' => 'shop_group_name'])
            )
            ->add(
                (new DataColumn('category_name'))
                    ->setName($this->trans('Root category', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions(['field' => 'category_name'])
            )
            ->add(
                (new DataColumn('url'))
                    ->setName($this->trans('Main URL for this shop', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions(['field' => 'url'])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'admin_shops_edit',
                                        'route_param_name' => 'shopId',
                                        'route_param_field' => 'id_shop',
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                (new LinkRowAction('add_url'))
                                    ->setName($this->trans('Click here to set a URL for this shop.', [], 'Admin.Shopparameters.Notification'))
                                    ->setIcon('link')
                                    ->setOptions([
                                        'route' => 'admin_shop_urls_create',
                                        'route_param_name' => 'shopId',
                                        'route_param_field' => 'id_shop',
                                        'accessibility_checker' => $this->addShopUrlAccessibilityChecker,
                                    ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_shops_delete',
                                    'shopId',
                                    'id_shop',
                                    options: ['accessibility_checker' => $this->deleteAccessibilityChecker],
                                )
                            ),
                    ])
            );
    }

    protected function getFilters(): FilterCollectionInterface
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_shop', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search ID', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('id_shop')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search name', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('shop_group_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search name', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('shop_group_name')
            )
            ->add(
                (new Filter('category_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search name', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('category_name')
            )
            ->add(
                (new Filter('url', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search URL', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('url')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'admin_shops_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    protected function getGridActions(): GridActionCollectionInterface
    {
        return (new GridActionCollection())
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
}
