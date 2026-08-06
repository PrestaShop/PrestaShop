<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class QuickAccessGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'quick_access';

    protected function getId(): string
    {
        return self::GRID_ID;
    }

    protected function getName(): string
    {
        return $this->trans('Quick Access', [], 'Admin.Navigation.Menu');
    }

    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk'))
                    ->setOptions(['bulk_field' => 'id_quick_access'])
            )
            ->add(
                (new DataColumn('id_quick_access'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions(['field' => 'id_quick_access'])
            )
            ->add(
                (new DataColumn('name'))
                    ->setName($this->trans('Name', [], 'Admin.Global'))
                    ->setOptions(['field' => 'name'])
            )
            ->add(
                (new DataColumn('link'))
                    ->setName($this->trans('URL', [], 'Admin.Global'))
                    ->setOptions(['field' => 'link'])
            )
            ->add(
                (new ToggleColumn('new_window'))
                    ->setName($this->trans('New tab', [], 'Admin.Navigation.Header'))
                    ->setOptions([
                        'field' => 'new_window',
                        'primary_field' => 'id_quick_access',
                        'route' => 'admin_quick_accesses_toggle_new_window',
                        'route_param_name' => 'quickAccessId',
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setIcon('edit')
                                    ->setOptions([
                                        'route' => 'admin_quick_accesses_edit',
                                        'route_param_name' => 'quickAccessId',
                                        'route_param_field' => 'id_quick_access',
                                        'clickable_row' => true,
                                    ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_quick_accesses_delete',
                                    'quickAccessId',
                                    'id_quick_access'
                                )
                            ),
                    ])
            );
    }

    public function getFilters()
    {
        return (new FilterCollection())
            ->add(
                (new Filter('id_quick_access', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search ID', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('id_quick_access')
            )
            ->add(
                (new Filter('name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search name', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('name')
            )
            ->add(
                (new Filter('link', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search URL', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('link')
            )
            ->add(
                (new Filter('new_window', YesAndNoChoiceType::class))
                    ->setAssociatedColumn('new_window')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => ['filterId' => self::GRID_ID],
                        'redirect_route' => 'admin_quick_accesses_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
    }

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

    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add($this->buildBulkDeleteAction('admin_quick_accesses_bulk_delete'));
    }
}
