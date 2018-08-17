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

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class RequestSqlGridDefinitionFactory is responsible for creating RequestSql grid definition
 */
final class RequestSqlGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var string
     */
    private $resetSearchUrl;

    /**
     * @var string
     */
    private $redirectionUrl;

    /**
     * @param string $resetSearchUrl
     * @param string $redirectionUrl
     */
    public function __construct($resetSearchUrl, $redirectionUrl)
    {
        $this->resetSearchUrl = $resetSearchUrl;
        $this->redirectionUrl = $redirectionUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'request_sql';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('SQL Manager', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_request_sql',
                ])
            )
            ->add((new DataColumn('id_request_sql'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_request_sql',
                ])
            )
            ->add((new DataColumn('name'))
                ->setName($this->trans('SQL query Name', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'name',
                ])
            )
            ->add((new DataColumn('sql'))
                ->setName($this->trans('SQL query', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'sql',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Global.Actions'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('export'))
                            ->setIcon('cloud_download')
                            ->setOptions([
                                'route' => 'admin_request_sql_export',
                                'route_param_name' => 'requestSqlId',
                                'route_param_field' => 'id_request_sql',
                            ])
                        )
                        ->add((new LinkRowAction('view'))
                            ->setName($this->trans('View', [], 'Admin.Global'))
                            ->setIcon('remove_red_eye')
                            ->setOptions([
                                'route' => 'admin_request_sql_view',
                                'route_param_name' => 'requestSqlId',
                                'route_param_field' => 'id_request_sql',
                            ])
                        )
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Global'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_request_sql_edit',
                                'route_param_name' => 'requestSqlId',
                                'route_param_field' => 'id_request_sql',
                            ])
                        )
                        ->add((new LinkRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'confirm_message' => $this->trans('Delete selected item?', [], 'Admin.Notifications.Warning'),
                                'route' => 'admin_request_sql_delete',
                                'route_param_name' => 'requestSqlId',
                                'route_param_field' => 'id_request_sql',
                            ])
                        ),
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return (new FilterCollection())
            ->add((new Filter('id_request_sql', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('id_request_sql')
            )
            ->add((new Filter('name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('name')
            )
            ->add((new Filter('sql', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                ])
                ->setAssociatedColumn('sql')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions([
                    'attr' => [
                        'data-url' => $this->resetSearchUrl,
                        'data-redirect' => $this->redirectionUrl,
                    ],
                ])
                ->setAssociatedColumn('actions')
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('delete_all'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_request_sql_delete_bulk',
                    'submit_method' => 'POST',
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning'),
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
            ->add((new SimpleGridAction('common_refresh_list'))
                ->setName($this->trans('Refresh list', [], 'Admin.Advparameters.Feature'))
                ->setIcon('refresh')
            )
            ->add((new SimpleGridAction('common_show_query'))
                ->setName($this->trans('Show SQL query', [], 'Admin.Actions'))
                ->setIcon('code')
            )
            ->add((new SimpleGridAction('common_export_sql_manager'))
                ->setName($this->trans('Export to SQL Manager', [], 'Admin.Actions'))
                ->setIcon('storage')
            )
        ;
    }
}
