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

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory\Security\Sessions;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\AbstractGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\DeleteActionTrait;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class EmployeeGridDefinitionFactory responsible for creating Employee grid definition.
 */
final class EmployeeGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use DeleteActionTrait;

    /**
     * @var string
     */
    private $resetActionUrl;

    /**
     * @var string
     */
    private $redirectionUrl;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param string $resetActionUrl
     * @param string $redirectionUrl
     * @param AccessibilityCheckerInterface $deleteProfileAccessibilityChecker
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        $resetActionUrl,
        $redirectionUrl
    ) {
        parent::__construct($hookDispatcher);
        $this->resetActionUrl = $resetActionUrl;
        $this->redirectionUrl = $redirectionUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'security_sessions_employees';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Employees sessions', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_employee_session',
                    ])
            )
            ->add(
                (new DataColumn('id_employee_session'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_employee_session',
                    ])
            )
            ->add(
                (new DataColumn('id_employee'))
                    ->setName($this->trans('Employee ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_employee',
                    ])
            )
            ->add(
                (new DataColumn('firstname'))
                    ->setName($this->trans('Firstname', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'firstname',
                    ])
            )
            ->add(
                (new DataColumn('lastname'))
                    ->setName($this->trans('Lastname', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'lastname',
                    ])
            )
            ->add(
                (new DataColumn('email'))
                    ->setName($this->trans('Email', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'email',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                            $this->buildDeleteAction(
                                'admin_security_sessions_employees_delete',
                                'sessionId',
                                'id_employee_session',
                                'DELETE'
                            )
                        ),
                    ])
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
    protected function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_employee_session', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'size' => 'small',
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('id_employee_session')
            )
            ->add(
                (new Filter('id_employee', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'size' => 'small',
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
                            'placeholder' => $this->trans('Firstname', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('firstname')
            )
            ->add(
                (new Filter('lastname', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Lastname', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('lastname')
            )
            ->add(
                (new Filter('email', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => [
                            'placeholder' => $this->trans('Email', [], 'Admin.Actions'),
                        ],
                    ])
                    ->setAssociatedColumn('email')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'data-url' => $this->resetActionUrl,
                            'data-redirect' => $this->redirectionUrl,
                        ],
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                (new SubmitBulkAction('bulk_delete_sessions_employees'))
                    ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                    ->setOptions([
                        'submit_route' => 'admin_security_sessions_employees_bulk_delete',
                        'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
                    ])
            );
    }
}
