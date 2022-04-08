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

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\Customer\DeleteCustomersBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\Customer\DeleteCustomerRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CustomerGridDefinitionFactory defines customers grid structure.
 */
final class CustomerGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'customer';

    /**
     * @var bool
     */
    private $isB2bFeatureEnabled;

    /**
     * @var bool
     */
    private $isMultistoreFeatureEnabled;

    /**
     * @var bool
     */
    private $isGroupsFeatureEnabled;

    /**
     * @var array
     */
    private $genderChoices;

    /**
     * @var array
     */
    private $groupChoices;

    /**
     * @var string
     */
    private $contextDateFormat;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param bool $isB2bFeatureEnabled
     * @param bool $isMultistoreFeatureEnabled
     * @param array $genderChoices
     * @param string $contextDateFormat
     * @param bool $isGroupsFeatureEnabled
     * @param array $groupChoices
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        $isB2bFeatureEnabled,
        $isMultistoreFeatureEnabled,
        array $genderChoices,
        string $contextDateFormat,
        bool $isGroupsFeatureEnabled = true,
        array $groupChoices = []
    ) {
        parent::__construct($hookDispatcher);
        $this->isB2bFeatureEnabled = $isB2bFeatureEnabled;
        $this->isMultistoreFeatureEnabled = $isMultistoreFeatureEnabled;
        $this->genderChoices = $genderChoices;
        $this->contextDateFormat = $contextDateFormat;
        $this->isGroupsFeatureEnabled = $isGroupsFeatureEnabled;
        $this->groupChoices = $groupChoices;
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
        return $this->trans('Manage your Customers', [], 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new BulkActionColumn('customers_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_customer',
                    ])
            )
            ->add(
                (new DataColumn('id_customer'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_customer',
                    ])
            )
            ->add(
                (new DataColumn('social_title'))
                    ->setName($this->trans('Social title', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'social_title',
                    ])
            )
            ->add(
                (new DataColumn('firstname'))
                    ->setName($this->trans('First name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'firstname',
                    ])
            )
            ->add(
                (new DataColumn('lastname'))
                    ->setName($this->trans('Last name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'lastname',
                    ])
            )
            ->add(
                (new DataColumn('email'))
                    ->setName($this->trans('Email address', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'email',
                    ])
            )
            ->add(
                (new BadgeColumn('total_spent'))
                    ->setName($this->trans('Sales', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'total_spent',
                        'empty_value' => '--',
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Enabled', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_customer',
                        'route' => 'admin_customers_toggle_status',
                        'route_param_name' => 'customerId',
                    ])
            )
            ->add(
                (new ToggleColumn('newsletter'))
                    ->setName($this->trans('Newsletter', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'newsletter',
                        'primary_field' => 'id_customer',
                        'route' => 'admin_customers_toggle_newsletter_subscription',
                        'route_param_name' => 'customerId',
                    ])
            )
            ->add(
                (new ToggleColumn('optin'))
                    ->setName($this->trans('Partner offers', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'field' => 'optin',
                        'primary_field' => 'id_customer',
                        'route' => 'admin_customers_toggle_partner_offer_subscription',
                        'route_param_name' => 'customerId',
                    ])
            )
            ->add(
                (new DateTimeColumn('date_add'))
                    ->setName($this->trans('Registration', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'format' => $this->contextDateFormat,
                        'field' => 'date_add',
                    ])
            )
            ->add(
                (new DateTimeColumn('connect'))
                    ->setName($this->trans('Last visit', [], 'Admin.Orderscustomers.Feature'))
                    ->setOptions([
                        'format' => $this->contextDateFormat,
                        'field' => 'connect',
                        'empty_data' => '--',
                    ])
            )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            ->setOptions([
                'actions' => (new RowActionCollection())
                    ->add(
                        (new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_customers_edit',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                            ])
                    )
                    ->add(
                        (new LinkRowAction('view'))
                            ->setName($this->trans('View', [], 'Admin.Actions'))
                            ->setIcon('zoom_in')
                            ->setOptions([
                                'route' => 'admin_customers_view',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                                'clickable_row' => true,
                            ])
                    )
                    ->add((new DeleteCustomerRowAction('delete'))
                    ->setName($this->trans('Delete', [], 'Admin.Actions'))
                    ->setIcon('delete')
                    ->setOptions([
                        'customer_id_field' => 'id_customer',
                        'customer_delete_route' => 'admin_customers_delete',
                    ])
                    ),
            ])
            );

        if ($this->isB2bFeatureEnabled) {
            $columns->addAfter(
                'email',
                (new DataColumn('company'))
                    ->setName($this->trans('Company', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'company',
                    ])
            );
        }

        if ($this->isMultistoreFeatureEnabled) {
            $columns->addBefore(
                'actions',
                (new DataColumn('shop_name'))
                    ->setName($this->trans('Shop', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'shop_name',
                        'sortable' => false,
                    ])
            );
        }

        if ($this->isGroupsFeatureEnabled) {
            $columns->addAfter(
                'email',
                (new DataColumn('default_group'))
                    ->setName($this->trans('Group', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'default_group',
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
                (new Filter('id_customer', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_customer')
            )
            ->add(
                (new Filter('social_title', ChoiceType::class))
                    ->setTypeOptions([
                        'choices' => $this->genderChoices,
                        'expanded' => false,
                        'multiple' => false,
                        'required' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->setAssociatedColumn('social_title')
            )
            ->add(
                (new Filter('firstname', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search first name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('firstname')
            )
            ->add(
                (new Filter('lastname', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search last name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('lastname')
            )
            ->add(
                (new Filter('email', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search email', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('email')
            )
            ->add(
                (new Filter('active', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('active')
            )
            ->add(
                (new Filter('newsletter', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('newsletter')
            )
            ->add(
                (new Filter('optin', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('optin')
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
                        'redirect_route' => 'admin_customers_index',
                    ])
                    ->setAssociatedColumn('actions')
            );

        if ($this->isB2bFeatureEnabled) {
            $filters->add(
                (new Filter('company', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search company', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('company')
            );
        }

        if ($this->isGroupsFeatureEnabled) {
            $filters->add(
                (new Filter('default_group', ChoiceType::class))
                    ->setTypeOptions([
                        'choices' => $this->groupChoices,
                        'expanded' => false,
                        'multiple' => false,
                        'required' => false,
                        'choice_translation_domain' => false,
                    ])
                    ->setAssociatedColumn('default_group')
                );
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
                            'import_type' => 'customers',
                        ],
                    ])
            )
            ->add(
                (new LinkGridAction('export'))
                    ->setName($this->trans('Export', [], 'Admin.Actions'))
                    ->setIcon('cloud_download')
                    ->setOptions([
                        'route' => 'admin_customers_export',
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
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_customers_enable_bulk',
                    ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_customers_disable_bulk',
                    ])
            )
            ->add((new DeleteCustomersBulkAction('delete_selection'))
            ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
            ->setOptions([
                'customers_bulk_delete_route' => 'admin_customers_delete_bulk',
            ])
            );
    }
}
