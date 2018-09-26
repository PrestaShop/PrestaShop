<?php
/**
 * 2007-2018 PrestaShop.
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
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\Catalog\Category\DeleteCategoriesBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Catalog\Category\DeleteCategoryRowAction;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Catalog\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class CategoryGridDefinitionFactory builds Grid definition for Categories listing.
 */
final class CategoryGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var string
     */
    private $resetActionUrl;

    /**
     * @var string
     */
    private $redirectActionUrl;

    /**
     * @var AccessibilityCheckerInterface
     */
    private $categoryForViewAccessibilityChecker;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @param string $resetActionUrl
     * @param string $redirectActionUrl
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param AccessibilityCheckerInterface $categoryForViewAccessibilityChecker
     */
    public function __construct(
        $resetActionUrl,
        $redirectActionUrl,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        AccessibilityCheckerInterface $categoryForViewAccessibilityChecker
    ) {
        $this->resetActionUrl = $resetActionUrl;
        $this->redirectActionUrl = $redirectActionUrl;
        $this->categoryForViewAccessibilityChecker = $categoryForViewAccessibilityChecker;
        $this->multistoreContextChecker = $multistoreContextChecker;
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'Categories';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Categories', [], 'Admin.Global');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_category',
                ])
            )
            ->add((new DataColumn('id_category'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_category',
                ])
            )
            ->add((new LinkColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'name',
                    'route' => 'admin_category_edit',
                    'route_param_name' => 'categoryId',
                    'route_param_field' => 'id_category',
                ])
            )
            ->add((new DataColumn('description'))
                ->setName($this->trans('Description', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'description',
                ])
            )
            ->add((new ToggleColumn('active'))
                ->setName($this->trans('Displayed', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'primary_field' => 'id_category',
                    'route' => 'admin_category_process_status_toggle',
                    'route_param_name' => 'categoryId',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => $this->getRowActions(),
                ])
            )
        ;

        if ($this->multistoreContextChecker->isSingleShopContext()) {
            $columns->addAfter('description', (new PositionColumn('position'))
                ->setName($this->trans('Position', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'position',
                    'id_field' => 'id_category',
                    'id_parent_field' => 'id_parent',
                    'position_update_route' => 'AdminCategories',
                ])
            );
        }

        return $columns;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $filters = (new FilterCollection())
            ->add((new Filter('id_category', TextType::class))
                ->setAssociatedColumn('id_category')
                ->setTypeOptions([
                    'required' => false,
                ])
            )
            ->add((new Filter('name', TextType::class))
                ->setAssociatedColumn('name')
                ->setTypeOptions([
                    'required' => false,
                ])
            )
            ->add((new Filter('description', TextType::class))
                ->setAssociatedColumn('description')
                ->setTypeOptions([
                    'required' => false,
                ])
            )
            ->add((new Filter('active', YesAndNoChoiceType::class))
                ->setAssociatedColumn('active')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setAssociatedColumn('actions')
                ->setTypeOptions([
                    'attr' => [
                        'data-url' => $this->resetActionUrl,
                        'data-redirect' => $this->redirectActionUrl,
                    ],
                ])
            )
        ;

        if ($this->multistoreContextChecker->isSingleShopContext()) {
            $filters->add((new Filter('position', TextType::class))
                ->setAssociatedColumn('position')
                ->setTypeOptions([
                    'required' => false,
                ])
            );
        }

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    protected function getGridActions()
    {
        return (new GridActionCollection())
            ->add((new LinkGridAction('import'))
                ->setName($this->trans('Import', [], 'Admin.Actions'))
                ->setIcon('cloud_upload')
                ->setOptions([
                    'route' => 'admin_import',
                    'route_params' => [
                        'import_type' => 'categories',
                    ],
                ])
            )
            ->add((new LinkGridAction('export'))
                ->setName($this->trans('Export', [], 'Admin.Actions'))
                ->setIcon('cloud_download')
                ->setOptions([
                    'route' => 'admin_category_export',
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
            ->add((new SubmitBulkAction('enable_selection'))
                ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_category_process_bulk_status_enable',
                ])
            )
            ->add((new SubmitBulkAction('disable_selection'))
                ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_category_process_bulk_status_disable',
                ])
            )
            ->add((new DeleteCategoriesBulkAction('delete_selection'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'categories_bulk_delete_route' => 'admin_category_process_bulk_delete',
                ])
            )
        ;
    }

    /**
     * @return RowActionCollection
     */
    private function getRowActions()
    {
        return (new RowActionCollection())
            ->add((new LinkRowAction('view'))
                ->setName($this->trans('View', [], 'Admin.Actions'))
                ->setIcon('zoom_in')
                ->setOptions([
                    'route' => 'admin_category_listing',
                    'route_param_name' => 'id_category',
                    'route_param_field' => 'id_category',
                    'accessibility_checker' => $this->categoryForViewAccessibilityChecker,
                ])
            )
            ->add((new LinkRowAction('edit'))
                ->setName($this->trans('Edit', [], 'Admin.Actions'))
                ->setIcon('edit')
                ->setOptions([
                    'route' => 'admin_category_edit',
                    'route_param_name' => 'categoryId',
                    'route_param_field' => 'id_category',
                ])
            )
            ->add((new DeleteCategoryRowAction('delete'))
                ->setName($this->trans('Delete', [], 'Admin.Actions'))
                ->setIcon('delete')
                ->setOptions([
                    'category_id_field' => 'id_category',
                    'category_delete_route' => 'admin_category_process_delete',
                ])
            )
        ;
    }
}
