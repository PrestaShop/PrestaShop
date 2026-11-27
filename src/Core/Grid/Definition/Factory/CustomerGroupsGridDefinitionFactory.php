<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\DeleteCustomerGroupAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class CustomerGroupsGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'customer_groups';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private readonly LanguageContext $languageContext,
        private readonly DeleteCustomerGroupAccessibilityChecker $deleteCustomerGroupAccessibilityChecker,
    ) {
        parent::__construct($hookDispatcher);
    }

    /**
     * {@inheritDoc}
     */
    protected function getId()
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritDoc}
     */
    protected function getName()
    {
        return $this->trans('Customer groups', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritDoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_group',
                        'disabled_field' => 'unremovable',
                    ])
            )
            ->add(
                (new DataColumn('id_group'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_group',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Group name', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('reduction'))
                    ->setName($this->trans('Discount (%)', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'reduction',
                    ])
            )
            ->add(
                (new DataColumn('members'))
                    ->setName($this->trans('Members', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'members',
                    ])
            )
            ->add(
                (new BooleanColumn('show_prices'))
                    ->setName($this->trans('Show prices', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'show_prices',
                        'true_name' => $this->trans('Yes', [], 'Admin.Global'),
                        'false_name' => $this->trans('No', [], 'Admin.Global'),
                    ])
            )
            ->add(
                (new DateTimeColumn('date_add'))
                    ->setName($this->trans('Creation date', [], 'Admin.Shopparameters.Feature'))
                    ->setOptions([
                        'field' => 'date_add',
                        'format' => $this->languageContext->getDateFormat(),
                    ])
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
                                        'route' => 'admin_customer_groups_edit',
                                        'route_param_name' => 'groupId',
                                        'route_param_field' => 'id_group',
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                (new LinkRowAction('view'))
                                    ->setName($this->trans('View', [], 'Admin.Actions'))
                                    ->setIcon('zoom_in')
                                    ->setOptions([
                                        'route' => 'admin_customer_groups_view',
                                        'route_param_name' => 'groupId',
                                        'route_param_field' => 'id_group',
                                    ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_customer_groups_delete',
                                    'groupId',
                                    'id_group',
                                    Request::METHOD_DELETE,
                                    [],
                                    ['accessibility_checker' => $this->deleteCustomerGroupAccessibilityChecker]
                                )
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_group', NumberType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('id_group')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search name', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('reduction', NumberType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'scale' => 2,
                        'attr' => [
                            'placeholder' => $this->trans('Search', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('reduction')
            )
            ->add(
                (new Filter('members', NumberType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('members')
            )
            ->add(
                (new Filter('show_prices', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('show_prices')
            )
            ->add(
                (new Filter('date_add', DateRangeType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('date_add')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_customer_groups_index',
                    ])
                    ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
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

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('admin_customer_groups_bulk_delete')
            );
    }
}
