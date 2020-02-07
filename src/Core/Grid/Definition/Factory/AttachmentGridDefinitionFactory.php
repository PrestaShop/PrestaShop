<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Defines attachments list grid
 */
final class AttachmentGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;

    public const GRID_ID = 'attachment';

    /**
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(HookDispatcherInterface $hookDispatcher)
    {
        parent::__construct($hookDispatcher);
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
        return $this->trans('Files', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add(
                (new BulkActionColumn('files_bulk'))
                    ->setOptions([
                        'bulk_field' => 'id_attachment',
                    ])
            )
            ->add(
                (new DataColumn('id_attachment'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_attachment',
                    ])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'name',
                    ])
            )
            ->add(
                (new DataColumn('file'))
                    ->setName($this->trans('File', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'file',
                    ])
            )
            ->add(
                (new DataColumn('file_size'))
                    ->setName($this->trans('Size', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'file_size',
                    ])
            )
            ->add(
                (new DataColumn('products'))
                    ->setName($this->trans('Products', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'products',
                    ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add(
                            (new LinkRowAction('edit'))
                                ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                ->setIcon('edit')
                                ->setOptions([
                                    'route' => 'admin_attachment_edit',
                                    'route_param_name' => 'attachmentId',
                                    'route_param_field' => 'id_attachment',
                                ])
                        )
                        ->add(
                            (new LinkRowAction('view'))
                                ->setName($this->trans('View', [], 'Admin.Actions'))
                                ->setIcon('zoom_in')
                                ->setOptions([
                                    'route' => 'admin_attachment_view',
                                    'route_param_name' => 'attachmentId',
                                    'route_param_field' => 'id_attachment',
                                ])
                        )
                        ->add(
                            (new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'route' => 'admin_attachment_delete',
                                'route_param_name' => 'attachmentId',
                                'route_param_field' => 'id_attachment',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                                'confirm_message_type' => SubmitRowAction::MESSAGE_TYPE_DYNAMIC,
                                'dynamic_message_field' => 'dynamic_message',
                            ])
                        ),
                ])
            );

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = (new FilterCollection())
            ->add(
                (new Filter('id_attachment', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('id_attachment')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search file name', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('file_size', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Search file size', [], 'Admin.Actions'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('file_size')
            )
            ->add(
                (new Filter('products', NumberType::class))
                    ->setTypeOptions([
                        'attr' => [
                            'placeholder' => $this->trans('Products', [], 'Admin.Catalog.Feature'),
                        ],
                        'required' => false,
                    ])
                    ->setAssociatedColumn('products')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setAssociatedColumn('actions')
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search',
                        'reset_route_params' => [
                            'controller' => 'attachment',
                            'action' => 'index',
                        ],
                        'redirect_route' => 'admin_attachments_index',
                    ])
            );

        return $filters;
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
                $this->buildBulkDeleteAction('admin_attachments_delete_bulk')
            );
    }
}
