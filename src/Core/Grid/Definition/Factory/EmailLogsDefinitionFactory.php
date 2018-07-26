<?php

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SubmitGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnFilterOption;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetFormType;

/**
 * Class EmailLogsDefinitionFactory is responsible for creating email logs definition
 */
final class EmailLogsDefinitionFactory extends AbstractGridDefinitionFactory
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
        return 'email_logs';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('E-mail', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('delete_email_logs'))
                ->setOptions([
                    'bulk_field' => 'id_mail',
                ])
            )
            ->add((new DataColumn('id_mail'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_mail',
                ])
            )
            ->add((new DataColumn('recipient'))
                ->setName($this->trans('Recipient', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'recipient',
                ])
            )
            ->add((new DataColumn('template'))
                ->setName($this->trans('Template', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'template',
                ])
            )
            ->add((new DataColumn('language'))
                ->setName($this->trans('Language', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'language',
                ])
            )
            ->add((new DataColumn('subject'))
                ->setName($this->trans('Subject', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'subject',
                ])
            )
            ->add((new DataColumn('date_add'))
                ->setName($this->trans('Sent', [], 'Admin.Advparameters.Feature'))
                ->setOptions([
                    'field' => 'date_add',
                    'filter' => new ColumnFilterOption(DateRangeType::class, [
                        'required' => false,
                    ]),
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'filter' => new ColumnFilterOption(SearchAndResetFormType::class, [
                        'attr' => [
                            'data-url' => $this->resetActionUrl,
                            'data-redirect' => $this->redirectionUrl,
                        ],
                    ]),
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('delete'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_delete_single_email_log',
                                'route_param_name' => 'mailId',
                                'route_param_field' => 'id_mail',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        )
                    ,
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
            ->add((new SubmitGridAction('delete_all_email_logs'))
                ->setName($this->trans('Erase all', [], 'Admin.Advparameters.Feature'))
                ->setIcon('delete')
                ->setOptions([
                    'submit_route' => 'admin_delete_all_email_logs',
                    'confirm_message' => $this->trans('Are you sure?', [], 'Admin.Notifications.Warning')
                ])
            )
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

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('delete_email_logs'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_delete_selected_email_logs',
                    'confirm_message' => $this->trans('Delete selected items?', [], 'Admin.Notifications.Warning')
                ])
            )
        ;
    }
}
