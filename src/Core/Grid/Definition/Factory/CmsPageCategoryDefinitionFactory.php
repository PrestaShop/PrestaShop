<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\LinkColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Multistore\MultistoreContextCheckerInterface;
use PrestaShopBundle\Form\Admin\Type\ReorderPositionsButtonType;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class CmsPageCategoryDefinitionFactory builds Grid definition for Cms page category listing.
 */
final class CmsPageCategoryDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'cms_page_category';

    /**
     * @var int
     */
    private $cmsCategoryParentId;

    /**
     * @var MultistoreContextCheckerInterface
     */
    private $multistoreContextChecker;

    /**
     * @var bool
     */
    private $isMultiStoreFeatureUsed;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param RequestStack $requestStack
     * @param MultistoreContextCheckerInterface $multistoreContextChecker
     * @param bool $isMultiStoreFeatureUsed
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        RequestStack $requestStack,
        MultistoreContextCheckerInterface $multistoreContextChecker,
        $isMultiStoreFeatureUsed
    ) {
        parent::__construct($hookDispatcher);
        $this->setCmsPageCategoryParentId($requestStack);

        $this->multistoreContextChecker = $multistoreContextChecker;
        $this->isMultiStoreFeatureUsed = $isMultiStoreFeatureUsed;
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
        return $this->trans('Categories', [], 'Admin.Navigation.Menu');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columnCollection = (new ColumnCollection())
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
            ])
            )
            ->add((new ToggleColumn('active'))
            ->setName($this->trans('Displayed', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'active',
                'route' => 'admin_cms_pages_category_toggle',
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
                        'clickable_row' => true,
                    ])
                    )
                    ->add((new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_cms_pages_category_edit',
                        'route_param_name' => 'cmsCategoryId',
                        'route_param_field' => 'id_cms_category',
                    ])
                    )
                    ->add(
                        $this->buildDeleteAction(
                            'admin_cms_pages_category_delete',
                            'cmsCategoryId',
                            'id_cms_category',
                            Request::METHOD_DELETE
                        )
                    ),
            ])
            )
        ;

        if ($this->isAllShopContextOrShopFeatureIsNotUsed()) {
            $columnCollection
                ->addAfter(
                    'description',
                    (new PositionColumn('position'))
                        ->setName($this->trans('Position', [], 'Admin.Global'))
                        ->setOptions([
                            'id_field' => 'id_cms_category',
                            'position_field' => 'position',
                            'update_method' => 'POST',
                            'update_route' => 'admin_cms_pages_category_update_position',
                            'record_route_params' => [
                                'id_parent' => 'id_cms_category',
                            ],
                        ])
                );
        }

        return $columnCollection;
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $actionsTypeOptions = [
            'reset_route' => 'admin_common_reset_search_by_filter_id',
            'reset_route_params' => [
                'filterId' => self::GRID_ID,
            ],
            'redirect_route' => 'admin_cms_pages_index',
        ];

        if ($this->cmsCategoryParentId) {
            $actionsTypeOptions['redirect_route_params'] = [
                'id_cms_category' => $this->cmsCategoryParentId,
            ];

            $actionsTypeOptions['reset_route_params']['id_cms_category'] = $this->cmsCategoryParentId;
        }

        $filterCollection = (new FilterCollection())
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
            ->add((new Filter('active', YesAndNoChoiceType::class))
            ->setAssociatedColumn('active')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
            ->setTypeOptions($actionsTypeOptions)
            ->setAssociatedColumn('actions')
            )
        ;

        if ($this->isAllShopContextOrShopFeatureIsNotUsed()) {
            $filterCollection
                ->add((new Filter('position', ReorderPositionsButtonType::class))
                ->setAssociatedColumn('position')
                )
            ;
        }

        return $filterCollection;
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
                'submit_route' => 'admin_cms_pages_category_bulk_status_enable',
            ])
            )
            ->add((new SubmitBulkAction('disable_selection'))
            ->setName($this->trans('Disable selection', [], 'Admin.Actions'))
            ->setOptions([
                'submit_route' => 'admin_cms_pages_category_bulk_status_disable',
            ])
            )
            ->add(
                $this->buildBulkDeleteAction('admin_cms_pages_category_delete_bulk')
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

    /**
     * This function is required due to in cms_category contains position column - on ideal case cms_category_shop
     * should have this column configured instead.
     * In such case the condition would be $this->multistoreContextChecker->isSingleShopContext()
     *
     * @return bool
     */
    private function isAllShopContextOrShopFeatureIsNotUsed()
    {
        return $this->multistoreContextChecker->isAllShopContext() || !$this->isMultiStoreFeatureUsed;
    }
}
