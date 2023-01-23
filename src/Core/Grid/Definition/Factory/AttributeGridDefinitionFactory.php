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

use PrestaShop\PrestaShop\Core\AttributeGroup\AttributeGroupViewDataProviderInterface;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\SubmitBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\ModalOptions;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\LinkGridAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Attribute\AttributeColorColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\PositionColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines grid for attributes group > attributes list
 */
final class AttributeGridDefinitionFactory extends AbstractFilterableGridDefinitionFactory
{
    use DeleteActionTrait;

    public const GRID_ID = 'attribute';

    /**
     * @var int
     */
    private $attributeGroupId;

    /**
     * @var AttributeGroupViewDataProviderInterface
     */
    private $attributeGroupViewDataProvider;

    /**
     * @param HookDispatcherInterface $hookDispatcher
     * @param int $attributeGroupId
     * @param AttributeGroupViewDataProviderInterface $attributeGroupViewDataProvider
     */
    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        $attributeGroupId,
        AttributeGroupViewDataProviderInterface $attributeGroupViewDataProvider
    ) {
        parent::__construct($hookDispatcher);
        $this->attributeGroupId = $attributeGroupId;
        $this->attributeGroupViewDataProvider = $attributeGroupViewDataProvider;
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
        return $this->attributeGroupViewDataProvider->getAttributeGroupNameById((int) $this->attributeGroupId);
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns()
    {
        $columns = (new ColumnCollection())
            ->add((new BulkActionColumn('bulk'))
            ->setOptions([
                'bulk_field' => 'id_attribute',
            ])
            )
            ->add((new DataColumn('id_attribute'))
            ->setName($this->trans('ID', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'id_attribute',
            ])
            )
            ->add((new DataColumn('value'))
            ->setName($this->trans('Value', [], 'Admin.Global'))
            ->setOptions([
                'field' => 'value',
            ])
        );

        if ($this->attributeGroupViewDataProvider->isColorGroup($this->attributeGroupId)) {
            $columns->add((new AttributeColorColumn('color'))
                ->setName($this->trans('Color', [], 'Admin.Catalog.Feature'))
                ->setOptions([
                    'field' => 'color',
                ])
            );
        }

        $columns
            ->add((new PositionColumn('position'))
            ->setName($this->trans('Position', [], 'Admin.Global'))
            ->setOptions([
                'id_field' => 'id_attribute',
                'position_field' => 'position',
                'update_method' => 'POST',
                'update_route' => 'admin_attributes_update_position',
                'record_route_params' => [
                    'id_attribute_group' => 'attributeGroupId',
                ],
            ])
        )
            ->add((new ActionColumn('actions'))
            ->setName($this->trans('Actions', [], 'Admin.Global'))
            ->setOptions([
                'actions' => (new RowActionCollection())
                    ->add((new LinkRowAction('edit'))
                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                    ->setIcon('edit')
                    ->setOptions([
                        'route' => 'admin_attributes_edit',
                        'route_param_name' => 'attributeGroupId',
                        'route_param_field' => 'id_attribute_group',
                        'extra_route_params' => [
                            'attributeId' => 'id_attribute',
                        ],
                    ])
                    )
                    ->add(
                        $this->buildDeleteAction(
                            'admin_attributes_delete',
                            'attributeGroupId',
                            'id_attribute_group',
                            Request::METHOD_DELETE,
                             [
                                 'attributeId' => 'id_attribute',
                             ]
                        )
                    ),
            ])
        );

        return $columns;
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
                    'import_type' => 'attributes',
                ],
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
    protected function getFilters()
    {
        $filters = (new FilterCollection())
            ->add((new Filter('id_attribute', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search ID', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('id_attribute')
            )
            ->add((new Filter('value', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search value', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('value')
            )
            ->add((new Filter('position', TextType::class))
            ->setTypeOptions([
                'required' => false,
                'attr' => [
                    'placeholder' => $this->trans('Search position', [], 'Admin.Actions'),
                ],
            ])
            ->setAssociatedColumn('position')
            )
            ->add((new Filter('actions', SearchAndResetType::class))
            ->setAssociatedColumn('actions')
            ->setTypeOptions([
                'reset_route' => 'admin_common_reset_search_by_filter_id',
                'reset_route_params' => [
                    'filterId' => self::GRID_ID,
                ],
                'redirect_route' => 'admin_attributes_index',
                'redirect_route_params' => [
                    'attributeGroupId' => $this->attributeGroupId,
                ],
            ])
            ->setAssociatedColumn('actions')
            );

        if ($this->attributeGroupViewDataProvider->isColorGroup($this->attributeGroupId)) {
            $filters->add((new Filter('color', TextType::class))
                ->setTypeOptions([
                    'required' => false,
                    'attr' => [
                        'placeholder' => $this->trans('Search color', [], 'Admin.Actions'),
                    ],
                ])
                ->setAssociatedColumn('color')
            );
        }

        return $filters;
    }

    /**
     * {@inheritdoc}
     */
    protected function getBulkActions()
    {
        return (new BulkActionCollection())
            ->add((new SubmitBulkAction('delete_selection'))
            ->setName($this->trans('Delete selected', [], 'Admin.Actions'))
            ->setOptions([
                'submit_route' => 'admin_attributes_bulk_delete',
                'route_params' => [
                    'attributeGroupId' => $this->attributeGroupId,
                ],
                'confirm_message' => $this->trans('Are you sure you want to delete the selected item(s)?', [], 'Admin.Global'),
                'modal_options' => new ModalOptions([
                    'title' => $this->trans('Delete selection', [], 'Admin.Actions'),
                    'confirm_button_label' => $this->trans('Delete', [], 'Admin.Actions'),
                    'confirm_button_class' => 'btn-danger',
                ]),
            ])
            );
    }
}
