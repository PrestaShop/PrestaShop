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

use PrestaShop\PrestaShop\Core\Grid\Action\GridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnFilterOption;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Employee\EmployeeNameWithAvatarColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Status\SeverityLevelColumn;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetFormType;

/**
 * Class LogGridDefinitionFactory is responsible for creating new instance of Log grid definition
 */
final class LogGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var string the URL to reset Grid filters.
     */
    private $resetActionUrl;

    /**
     * @var string the URL for redirection.
     */
    private $redirectionUrl;

    /**
     * LogGridDefinitionFactory constructor.
     * @param string $resetActionUrl
     * @param string $redirectionUrl
     */
    public function __construct($resetActionUrl, $redirectionUrl)
    {
        $this->resetActionUrl = $resetActionUrl;
        $this->redirectionUrl = $redirectionUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'logs';
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
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk_action'))
                ->setOptions([
                    'bulk_field' => 'id_log',
                ])
            )
            ->add(
                (new DataColumn('id_log'))
                ->setName($this->trans('ID', [], 'Global.Actions'))
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
                    'format' => 'Y-m-d H:i',
                    'filter' => new ColumnFilterOption(DateRangeType::class),
                    'field' => 'date_add',
                ])
            )
            ->add(
                (new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'filter' => new ColumnFilterOption(SearchAndResetFormType::class, [
                        'attr' => [
                            'data-url' => $this->resetActionUrl,
                            'data-redirect' => $this->redirectionUrl,
                        ],
                    ]),
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add(new GridAction(
                'delete',
                $this->trans('Erase all', [], 'Admin.Advparameters.Feature'),
                'delete_forever',
                'delete_all_logs'
            ))
            ->add(new GridAction(
                'common_refresh_list',
                $this->trans('Refresh list', [], 'Admin.Advparameters.Feature'),
                'refresh',
                'simple'
            ))
            ->add(new GridAction(
                'common_show_query',
                $this->trans('Show SQL query', [], 'Admin.Actions'),
                'code',
                'simple'
            ))
            ->add(new GridAction(
                'common_export_sql_manager',
                $this->trans('Export to SQL Manager', [], 'Admin.Actions'),
                'storage',
                'simple'
            ))
        ;
    }
}
