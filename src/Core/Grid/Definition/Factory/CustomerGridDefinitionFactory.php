<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BadgeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
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
    /**
     * @var bool
     */
    private $isB2bFeatureEnabled;

    /**
     * @var bool
     */
    private $isMultistoreFeatureEnabled;

    /**
     * @var array
     */
    private $genderChoices;

    /**
     * @param bool $isB2bFeatureEnabled
     * @param bool $isMultistoreFeatureEnabled
     * @param array $genderChoices
     */
    public function __construct(
        $isB2bFeatureEnabled,
        $isMultistoreFeatureEnabled,
        array $genderChoices
    ) {
        $this->isB2bFeatureEnabled = $isB2bFeatureEnabled;
        $this->isMultistoreFeatureEnabled = $isMultistoreFeatureEnabled;
        $this->genderChoices = $genderChoices;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'customer';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Manage your Customers', array(), 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new BulkActionColumn('customers_bulk'))
                    ->setOptions(array(
                    'bulk_field' => 'id_customer',
                ))
            )
            ->add(
                (new DataColumn('id_customer'))
                    ->setName($this->trans('ID', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'id_customer',
                ))
            )
            ->add(
                (new DataColumn('social_title'))
                    ->setName($this->trans('Social title', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'social_title',
                ))
            )
            ->add(
                (new DataColumn('firstname'))
                    ->setName($this->trans('First name', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'firstname',
                ))
            )
            ->add(
                (new DataColumn('lastname'))
                    ->setName($this->trans('Last name', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'lastname',
                ))
            )
            ->add(
                (new DataColumn('email'))
                    ->setName($this->trans('Email address', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'email',
                ))
            )
            ->add(
                (new BadgeColumn('total_spent'))
                    ->setName($this->trans('Sales', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'total_spent',
                    'empty_value' => '--',
                ))
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Enabled', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'active',
                    'primary_field' => 'id_customer',
                    'route' => 'admin_customers_index',
                    'route_param_name' => 'customerId',
                ))
            )
            ->add(
                (new ToggleColumn('newsletter'))
                    ->setName($this->trans('Newsletter', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'newsletter',
                    'primary_field' => 'id_customer',
                    'route' => 'admin_customers_index',
                    'route_param_name' => 'customerId',
                ))
            )
            ->add(
                (new ToggleColumn('optin'))
                    ->setName($this->trans('Partner offers', array(), 'Admin.Orderscustomers.Feature'))
                    ->setOptions(array(
                    'field' => 'optin',
                    'primary_field' => 'id_customer',
                    'route' => 'admin_customers_index',
                    'route_param_name' => 'customerId',
                ))
            )
            ->add(
                (new DataColumn('date_add'))
                    ->setName($this->trans('Registration', array(), 'Admin.Orderscustomers.Feature'))
                    ->setOptions(array(
                    'field' => 'date_add',
                ))
            )
            ->add(
                (new DataColumn('connect'))
                    ->setName($this->trans('Last visit', array(), 'Admin.Orderscustomers.Feature'))
                    ->setOptions(array(
                    'field' => 'connect',
                ))
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setOptions(array(
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new LinkRowAction('edit'))
                                ->setName($this->trans('Edit', array(), 'Admin.Actions'))
                                ->setIcon('edit')
                                ->setOptions(array(
                                'route' => 'admin_customers_edit',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                            ))
                        )
                        ->add(
                            (new LinkRowAction('view'))
                                ->setName($this->trans('View', array(), 'Admin.Actions'))
                                ->setIcon('zoom_in')
                                ->setOptions(array(
                                'route' => 'admin_customers_view',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                            ))
                        )
                        ->add(
                            (new SubmitRowAction('delete'))
                                ->setName($this->trans('Delete', array(), 'Admin.Actions'))
                                ->setIcon('delete')
                                ->setOptions(array(
                                'method' => 'DELETE',
                                'route' => 'admin_customers_index',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    array(),
                                    'Admin.Notifications.Warning'
                                ),
                            ))
                        ),
                ))
            )
        ;

        if ($this->isB2bFeatureEnabled) {
            $columns->addAfter(
                'email',
                (new DataColumn('company'))
                    ->setName($this->trans('Company', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'company',
                ))
            );
        }

        if ($this->isMultistoreFeatureEnabled) {
            $columns->addBefore(
                'actions',
                (new DataColumn('shop_name'))
                    ->setName($this->trans('Shop', array(), 'Admin.Global'))
                    ->setOptions(array(
                    'field' => 'shop_name',
                    'sortable' => false,
                ))
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
                    ->setTypeOptions(array(
                    'required' => false,
                ))
                    ->setAssociatedColumn('id_customer')
            )
            ->add(
                (new Filter('social_title', ChoiceType::class))
                    ->setTypeOptions(array(
                    'choices' => $this->genderChoices,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false,
                    'choice_translation_domain' => false,
                ))
                    ->setAssociatedColumn('social_title')
            )
            ->add(
                (new Filter('firstname', TextType::class))
                    ->setTypeOptions(array(
                    'required' => false,
                ))
                    ->setAssociatedColumn('firstname')
            )
            ->add(
                (new Filter('lastname', TextType::class))
                    ->setTypeOptions(array(
                    'required' => false,
                ))
                    ->setAssociatedColumn('lastname')
            )
            ->add(
                (new Filter('email', TextType::class))
                    ->setTypeOptions(array(
                    'required' => false,
                ))
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
                    ->setTypeOptions(array(
                    'required' => false,
                ))
                    ->setAssociatedColumn('date_add')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions(array(
                    'reset_route' => 'admin_common_reset_search',
                    'reset_route_params' => array(
                        'controller' => 'customer',
                        'action' => 'index',
                    ),
                    'redirect_route' => 'admin_customers_index',
                ))
                    ->setAssociatedColumn('actions')
            )
        ;

        if ($this->isB2bFeatureEnabled) {
            $filters->add(
                (new Filter('company', TextType::class))
                    ->setTypeOptions(array(
                    'required' => false,
                ))
                    ->setAssociatedColumn('company')
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
                    ->setName($this->trans('Import', array(), 'Admin.Actions'))
                    ->setIcon('cloud_upload')
                    ->setOptions(array(
                    'route' => 'admin_import',
                    'route_params' => array(
                        'import_type' => 'customers',
                    ),
                ))
            )
            ->add(
                (new LinkGridAction('export'))
                    ->setName($this->trans('Export', array(), 'Admin.Actions'))
                    ->setIcon('cloud_download')
                    ->setOptions(array(
                    'route' => 'admin_customers_index',
                ))
            )
            ->add(
                (new SimpleGridAction('common_refresh_list'))
                    ->setName($this->trans('Refresh list', array(), 'Admin.Advparameters.Feature'))
                    ->setIcon('refresh')
            )
            ->add(
                (new SimpleGridAction('common_show_query'))
                    ->setName($this->trans('Show SQL query', array(), 'Admin.Actions'))
                    ->setIcon('code')
            )
            ->add(
                (new SimpleGridAction('common_export_sql_manager'))
                    ->setName($this->trans('Export to SQL Manager', array(), 'Admin.Actions'))
                    ->setIcon('storage')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Enable selection', array(), 'Admin.Actions'))
                    ->setOptions(array(
                    'submit_route' => 'admin_customers_index',
                ))
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Disable selection', array(), 'Admin.Actions'))
                    ->setOptions(array(
                    'submit_route' => 'admin_customers_index',
                ))
            )
            ->add(
                (new SubmitBulkAction('delete_selection'))
                    ->setName($this->trans('Delete selected', array(), 'Admin.Actions'))
                    ->setOptions(array(
                    'submit_route' => 'admin_customers_index',
                ))
            )
        ;
    }
}
