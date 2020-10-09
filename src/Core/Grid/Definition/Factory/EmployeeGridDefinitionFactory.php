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
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\Common\Team\ProfileChoiceType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class EmployeeGridDefinitionFactory creates grid definition for Employee data.
 */
final class EmployeeGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    /**
     * @var string
     */
    private $resetUrl;

    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $resetUrl
     * @param string $redirectUrl
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        $resetUrl,
        $redirectUrl
    ) {
        parent::__construct($hookDispatcher);
        $this->resetUrl = $resetUrl;
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'employee';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Employees', [], 'Admin.Advparameters.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('employee_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_employee',
                    ])
            )
            ->add(
                (new DataColumn('id_employee'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_employee',
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
                (new DataColumn('profile'))
                    ->setName($this->trans('Profile', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'profile_name',
                    ])
            )
            ->add(
                (new ToggleColumn('active'))
                    ->setName($this->trans('Active', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'active',
                        'primary_field' => 'id_employee',
                        'route' => 'admin_employees_toggle_status',
                        'route_param_name' => 'employeeId',
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
                                'route' => 'admin_employees_edit',
                                'route_param_name' => 'employeeId',
                                'route_param_field' => 'id_employee',
                                'clickable_row' => true,
                            ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_employees_delete',
                                    'employeeId',
                                    'id_employee'
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
                (new Filter('id_employee', NumberType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('id_employee')
            )
            ->add(
                (new Filter('firstname', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search first name', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('firstname')
            )
            ->add(
                (new Filter('lastname', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search last name', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('lastname')
            )
            ->add(
                (new Filter('email', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Search email', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('email')
            )
            ->add(
                (new Filter('profile', ProfileChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('profile')
            )
            ->add((new Filter('active', YesAndNoChoiceType::class))
            ->setAssociatedColumn('active')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'data-url' => $this->resetUrl,
                            'data-redirect' => $this->redirectUrl,
                        ],
                    ])
                    ->setAssociatedColumn('actions')
            );
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
                (new SubmitBulkAction('enable_selection'))
                    ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_employees_bulk_enable_status',
                    ])
            )
            ->add(
                (new SubmitBulkAction('disable_selection'))
                    ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_employees_bulk_disable_status',
                    ])
            )
            ->add(
                $this->buildBulkDeleteAction('admin_employees_bulk_delete')
            );
    }
}
