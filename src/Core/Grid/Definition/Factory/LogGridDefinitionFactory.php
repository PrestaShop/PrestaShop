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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SubmitGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Employee\EmployeeNameWithAvatarColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Status\SeverityLevelColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\LogSeverityChoiceType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class LogGridDefinitionFactory is responsible for creating new instance of Log grid definition.
 */
final class LogGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    public const GRID_ID = 'logs';

    /**
     * @var string Date format for the current user
     */
    private $contextDateFormat;

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        string $contextDateFormat
    ) {
        parent::__construct($hookDispatcher);
        $this->contextDateFormat = $contextDateFormat;
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
        return $this->trans('Logs', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new DataColumn('id_log'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_log',
                    ])
            )
            ->add(
                (new EmployeeNameWithAvatarColumn('employee'))
                    ->setName($this->trans('Employee', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'employee',
                    ])
            )
            ->add(
                (new SeverityLevelColumn('severity'))
                    ->setName($this->trans('Severity (1-4)', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'with_message' => true,
                        'field' => 'severity',
                    ])
            )
            ->add(
                (new DataColumn('message'))
                    ->setName($this->trans('Message', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'message',
                    ])
            )
            ->add(
                (new DataColumn('object_type'))
                    ->setName($this->trans('Object type', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'object_type',
                    ])
            )
            ->add(
                (new DataColumn('object_id'))
                    ->setName($this->trans('Object ID', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'object_id',
                    ])
            )
            ->add(
                (new DataColumn('shop_name'))
                    ->setName($this->trans('Shop context', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'shop_name',
                    ])
            )
            ->add(
                (new DataColumn('language'))
                    ->setName($this->trans('Language', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'language',
                    ])
            )
            ->add(
                (new DataColumn('error_code'))
                    ->setName($this->trans('Error code', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'error_code',
                    ])
            )
            ->add(
                (new DateTimeColumn('date_add'))
                    ->setName($this->trans('Date', [], 'Admin.Global'))
                    ->setOptions([
                        'format' => $this->contextDateFormat,
                        'field' => 'date_add',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
            );

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_log', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_log')
            )
            ->add(
                (new Filter('employee', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('employee')
            )
            ->add(
                (new Filter('severity', LogSeverityChoiceType::class))
                    ->setAssociatedColumn('severity')
            )
            ->add(
                (new Filter('message', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('message')
            )
            ->add(
                (new Filter('object_type', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('object_type')
            )
            ->add(
                (new Filter('object_id', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('object_id')
            )
            ->add(
                (new Filter('error_code', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                    ])
                    ->setAssociatedColumn('error_code')
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
                        'redirect_route' => 'admin_logs_index',
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
                (new SubmitGridAction('delete_all_email_logs'))
                    ->setName($this->trans('Erase all', [], 'Admin.Advparameters.Feature'))
                    ->setIcon('delete')
                    ->setOptions([
                        'submit_route' => 'admin_logs_delete_all',
                        'confirm_message' => $this->trans('Are you sure?', [], 'Admin.Notifications.Warning'),
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
}
