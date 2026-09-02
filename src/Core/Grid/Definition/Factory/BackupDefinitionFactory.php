<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;

/**
 * Class BackupDefinitionFactory is responsible for defining 'Configure > Advanced Parameters > Database > Backup' grid.
 */
final class BackupDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'backup';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('DB backups', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('backup_bulk_file_names'))
                    ->setOptions([
                        'bulk_field' => 'file_name',
                    ])
            )
            ->add(
                (new DataColumn('date'))
                    ->setName($this->trans('Date', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'date_formatted',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('age'))
                    ->setName($this->trans('Age', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'age_formatted',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('file_name'))
                    ->setName($this->trans('Filename', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'file_name',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new DataColumn('file_size'))
                    ->setName($this->trans('File size', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'file_size_formatted',
                        'sortable' => false,
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('view'))
                                    ->setIcon('cloud_download')
                                    ->setOptions([
                                        'route' => 'admin_backups_download_view',
                                        'route_param_name' => 'downloadFileName',
                                        'route_param_field' => 'file_name',
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_backups_delete',
                                    'deleteFileName',
                                    'file_name'
                                )
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add(
                $this->buildBulkDeleteAction('admin_backups_bulk_delete')
            );
    }
}
