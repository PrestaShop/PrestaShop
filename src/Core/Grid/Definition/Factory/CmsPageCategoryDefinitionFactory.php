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
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\SubmitRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class CmsPageCategoryDefinitionFactory builds Grid definition for Cms page category listing.
 */
final class CmsPageCategoryDefinitionFactory extends AbstractGridDefinitionFactory
{
    /**
     * @var int
     */
    private $cmsCategoryParentId;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->setCmsPageCategoryParentId($requestStack);
    }

    /**
     * {@inheritdoc}
     */
    protected function getId()
    {
        return 'cms_page_category';
    }

    /**
     * {@inheritdoc}
     */
    protected function getName()
    {
        return $this->trans('Categories', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        return (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
                ->setOptions([
                    'bulk_field' => 'id_cms_category',
                ])
            )
            ->add((new DataColumn('id_cms_category'))
                ->setName($this->trans('ID', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_cms_category',
                ])
            )
            ->add((new LinkColumn('name'))
                ->setName($this->trans('Name', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'name',
                    'route' => 'admin_cms_pages_index',
                    'route_param_name' => 'id_cms_category',
                    'route_param_field' => 'id_cms_category',
                ])
            )
            ->add((new DataColumn('description'))
                ->setName($this->trans('Description', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'description',
                    'sortable' => false,
                ])
            )
            ->add((new DataColumn('position'))
                ->setName($this->trans('Position', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'id_cms_category',
                ])
            )
            ->add((new ToggleColumn('active'))
                ->setName($this->trans('Displayed', [], 'Admin.Global'))
                ->setOptions([
                    'field' => 'active',
                    'route' => 'admin_cms_pages_toggle_cms_category',
                    'primary_field' => 'id_cms_category',
                    'route_param_name' => 'cmsCategoryId',
                ])
            )
            ->add((new ActionColumn('actions'))
                ->setName($this->trans('Actions', [], 'Admin.Global'))
                ->setOptions([
                    'actions' => (new RowActionCollection())
                        ->add((new LinkRowAction('view'))
                            ->setName($this->trans('View', [], 'Admin.Actions'))
                            ->setIcon('zoom_in')
                            ->setOptions([
                                'route' => 'admin_cms_pages_index',
                                'route_param_name' => 'id_cms_category',
                                'route_param_field' => 'id_cms_category',
                            ])
                        )
                        ->add((new LinkRowAction('edit'))
                            ->setName($this->trans('Edit', [], 'Admin.Actions'))
                            ->setIcon('edit')
                            ->setOptions([
                                'route' => 'admin_cms_pages_edit_cms_category',
                                'route_param_name' => 'cmsCategoryId',
                                'route_param_field' => 'id_cms_category',
                            ])
                        )
                        ->add((new SubmitRowAction('delete'))
                            ->setName($this->trans('Delete', [], 'Admin.Actions'))
                            ->setIcon('delete')
                            ->setOptions([
                                'method' => 'DELETE',
                                'route' => 'admin_cms_pages_delete_cms_category',
                                'route_param_name' => 'cmsCategoryId',
                                'route_param_field' => 'id_cms_category',
                                'confirm_message' => $this->trans(
                                    'Delete selected item?',
                                    [],
                                    'Admin.Notifications.Warning'
                                ),
                            ])
                        ),
                ])
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $actionsTypeOptions = [
            'reset_route' => 'admin_common_reset_search',
            'reset_route_params' => [
                'controller' => 'CmsPage',
                'action' => 'index',
            ],
            'redirect_route' => 'admin_cms_pages_index',
        ];

        if ($this->cmsCategoryParentId) {
            $actionsTypeOptions['redirect_route_params'] = [
                'id_cms_category' => $this->cmsCategoryParentId,
            ];
        }

        return (new FilterCollection())
            ->add((new Filter('id_cms_category', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('ID', [], 'Admin.Global'),
                    ],
                ])
                ->setAssociatedColumn('id_cms_category')
            )
            ->add((new Filter('name', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Name', [], 'Admin.Global'),
                    ],
                ])
                ->setAssociatedColumn('name')
            )
            ->add((new Filter('description', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Description', [], 'Admin.Global'),
                    ],
                ])
                ->setAssociatedColumn('description')
            )
            ->add((new Filter('position', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Position', [], 'Admin.Global'),
                    ],
                ])
                ->setAssociatedColumn('position')
            )
            ->add((new Filter('active', YesAndNoChoiceType::class))
                ->setAssociatedColumn('active')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
                ->setTypeOptions($actionsTypeOptions)
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
            ->add((new SubmitBulkAction('enable_selection'))
                ->setName($this->trans('Enable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_cms_pages_bulk_status_enable',
                ])
            )
            ->add((new SubmitBulkAction('disable_selection'))
                ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_cms_pages_bulk_status_disable',
                ])
            )
            ->add((new SubmitBulkAction('delete_bulk'))
                ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
                ->setOptions([
                    'submit_route' => 'admin_cms_pages_delete_bulk_cms_category',
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

    /**
     * Sets cms page category parent id directly from request attribute. On not found case, it assigns the default one.
     *
     * @param RequestStack $requestStack
     */
    private function setCmsPageCategoryParentId(RequestStack $requestStack)
    {
        $request = $requestStack->getCurrentRequest();

        if (null !== $request && $request->query->getInt('id_cms_category')) {
            $this->cmsCategoryParentId = $request->query->getInt('id_cms_category');
        }
    }
}
