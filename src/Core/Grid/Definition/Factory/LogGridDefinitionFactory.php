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

use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class LogGridDefinitionFactory is responsible for creating new instance of Log grid definition
 */
final class LogGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var ColumnInterface
     */
    private $employeeNameWithAvatarColumn;

    /**
     * @param EngineInterface $templating
     * @param ColumnInterface $employeeNameWithAvatarColumn
     */
    public function __construct(EngineInterface $templating, ColumnInterface $employeeNameWithAvatarColumn)
    {
        $this->templating = $templating;
        $this->employeeNameWithAvatarColumn = $employeeNameWithAvatarColumn;
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
        $columns = ColumnCollection::fromArray([
            [
                'id' => 'id_log',
                'name' => $this->trans('ID', [], 'Admin.Global'),
                'filter_form_type' => TextType::class,
            ],
            [
                'id' => 'severity',
                'name' => $this->trans('Severity (1-4)', [], 'Admin.Advparameters.Feature'),
                'filter_form_type' => TextType::class,
            ],
            [
                'id' => 'message',
                'name' => $this->trans('Message', [], 'Admin.Global'),
                'filter_form_type' => TextType::class,
            ],
            [
                'id' => 'object_type',
                'name' => $this->trans('Object type', [], 'Admin.Advparameters.Feature'),
                'filter_form_type' => TextType::class,
            ],
            [
                'id' => 'object_id',
                'name' => $this->trans('Object ID', [], 'Admin.Advparameters.Feature'),
                'filter_form_type' => TextType::class,
            ],
            [
                'id' => 'error_code',
                'name' => $this->trans('Error code', [], 'Admin.Advparameters.Feature'),
                'filter_form_type' => TextType::class,
            ],
            [
                'id' => 'date_add',
                'name' => $this->trans('Date', [], 'Admin.Global'),
                'filter_form_type' => DateRangeType::class,
            ],
        ]);

        $columns->addAfter('id_log', $this->employeeNameWithAvatarColumn);

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        $templating = $this->templating;
        $renderDeleteAllAction = function () use ($templating) {
            return $templating->render('@AdvancedParameters/LogsPage/Blocks/delete_all_grid_action.html.twig');
        };

        return GridActionCollection::fromArray([
            [
                'id' => 'delete',
                'name' => $this->trans('Erase all', [], 'Admin.Advparameters.Feature'),
                'icon' => 'delete_forever',
                'renderer' => $renderDeleteAllAction,
            ],
            [
                'id' => 'ps_refresh_list',
                'name' => $this->trans('Refresh list', [], 'Admin.Advparameters.Feature'),
                'icon' => 'refresh',
            ],
            [
                'id' => 'ps_show_query',
                'name' => $this->trans('Show SQL query', [], 'Admin.Actions'),
                'icon' => 'code',
            ],
            [
                'id' => 'ps_export_sql_manager',
                'name' => $this->trans('Export to SQL Manager', [], 'Admin.Actions'),
                'icon' => 'storage',
            ],
        ]);
    }
}
