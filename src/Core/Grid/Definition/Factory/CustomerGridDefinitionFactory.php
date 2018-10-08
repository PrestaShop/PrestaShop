<?php
/**
 * 2007-2018 PrestaShop
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

use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
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
     * @param bool $isB2bFeatureEnabled
     * @param bool $isMultistoreFeatureEnabled
     */
    public function __construct($isB2bFeatureEnabled, $isMultistoreFeatureEnabled)
    {
        $this->isB2bFeatureEnabled = $isB2bFeatureEnabled;
        $this->isMultistoreFeatureEnabled = $isMultistoreFeatureEnabled;
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
        return $this->trans('Manage your Customers', [], 'Admin.Orderscustomers.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add((new BulkActionColumn('customers_bulk'))
                ->setOptions([
                    'bulk_field' => 'id_customer',
                ])
            )
            ->add((new DataColumn('id_customer'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_customer',
                ])
            )
            ->add((new DataColumn('social_title'))
                ->setName($this->trans('Social title', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'social_title',
                ])
            )
            ->add((new DataColumn('firstname'))
                ->setName($this->trans('First name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'firstname',
                ])
            )
            ->add((new DataColumn('lastname'))
                ->setName($this->trans('Last name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'lastname',
                ])
            )
            ->add((new DataColumn('email'))
                ->setName($this->trans('Email address', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'email',
                ])
            )
            ->add((new DataColumn('total_spent'))
                ->setName($this->trans('Sales', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'total_spent',
                ])
            )
            ->add((new ToggleColumn('active'))
                ->setName($this->trans('Enabled', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'primary_field' => 'id_customer',
                    'route' => 'admin_customers_index',
                    'route_param_id' => 'customerId',
                ])
            )
            ->add((new ToggleColumn('newsletter'))
                ->setName($this->trans('Newsletter', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'newsletter',
                    'primary_field' => 'id_customer',
                    'route' => 'admin_customers_index',
                    'route_param_id' => 'customerId',
                ])
            )
            ->add((new ToggleColumn('optin'))
                ->setName($this->trans('Partner offers', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'optin',
                    'primary_field' => 'id_customer',
                    'route' => 'admin_customers_index',
                    'route_param_id' => 'customerId',
                ])
            )
            ->add((new DataColumn('date_add'))
                ->setName($this->trans('Registration', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'date_add',
                ])
            )
            ->add((new DataColumn('connect'))
                ->setName($this->trans('Last visit', [], 'Admin.Orderscustomers.Feature'))
                ->setOptions([
                    'field' => 'connect',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('edit'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_customers_edit',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                            ])
                        )
                        ->add((new LinkRowAction('view'))
                            ->setName($this->trans('View', [], 'Admin.Actions'))
                            ->setIcon('zoom_in')
                            ->setOptions([
                                'route' => 'admin_customers_view',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'DELETE',
                                'route' => 'admin_customers_index',
                                'route_param_name' => 'customerId',
                                'route_param_field' => 'id_customer',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        )
                ])
            )
        ;

        if ($this->isB2bFeatureEnabled) {
            $columns->addAfter('email', (new DataColumn('company'))
                ->setName($this->trans('Company', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'company',
                ])
            );
        }

        if ($this->isMultistoreFeatureEnabled) {
            $columns->addBefore('actions', (new DataColumn('shop_name'))
                ->setName($this->trans('Shop', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'shop_name',
                    'sortable' => false,
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
            ->add((new Filter('id_customer', NumberType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('id_customer')
            )
            ->add((new Filter('social_title', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('social_title')
            )
            ->add((new Filter('firstname', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('firstname')
            )
            ->add((new Filter('lastname', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('lastname')
            )
            ->add((new Filter('email', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('email')
            )
            ->add((new Filter('total_spent', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('total_spent')
            )
            ->add((new Filter('active', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('active')
            )
            ->add((new Filter('newsletter', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('newsletter')
            )
            ->add((new Filter('optin', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('optin')
            )
            ->add((new Filter('date_add', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('date_add')
            )
            ->add((new Filter('connect', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('connect')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('actions')
            )
        ;

        if ($this->isB2bFeatureEnabled) {
            $filters->add((new Filter('company', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('company')
            );
        }

        return $filters;
    }
}
