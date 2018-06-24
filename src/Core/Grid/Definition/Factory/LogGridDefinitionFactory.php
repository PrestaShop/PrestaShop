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

use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Employee\EmployeeNameWithAvatarColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\SimpleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Status\SeverityLevelColumn;

/**
 * Class LogGridDefinitionFactory is responsible for creating new instance of Log grid definition
 */
final class LogGridDefinitionFactory extends AbstractGridDefinitionFactory
{
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
            ->add((new BulkActionColumn('bulk_action'))
                ->setOptions([
                    'bulk_value' => 'id_log',
                ])
            )
            ->add((new SimpleColumn('id_log'))
                ->setName($this->trans('ID', [], 'Global.Actions'))
            )
            ->add((new EmployeeNameWithAvatarColumn('employee'))
                ->setName($this->trans('Employee', [], ''))
            )
            ->add((new SeverityLevelColumn('severity'))
                ->setName($this->trans('Severity (1-4)', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'with_message' => true,
                ])
            )
            ->add((new SimpleColumn('message'))
                ->setName($this->trans('Message', [], 'Global.Actions'))
            )
            ->add((new SimpleColumn('object_type'))
                ->setName($this->trans('Object type', [], 'Admin.Advparameters.Feature'))
            )
            ->add((new SimpleColumn('object_id'))
                ->setName($this->trans('Object ID', [], 'Admin.Advparameters.Feature'))
            )
            ->add((new SimpleColumn('error_code'))
                ->setName($this->trans('Error code', [], 'Admin.Advparameters.Feature'))
            )
            ->add((new DateTimeColumn('date_add'))
                ->setName($this->trans('Date', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'format' => 'Y-m-d H:i',
                ])
            )
        ;
    }

    protected function getOptions()
    {
        return [
            'actions' => [
                'bulk' => $this->getBulkActions(),
                'grid' => $this->getPanelActions(),
                'row' => [],
            ],
        ];
    }
}
