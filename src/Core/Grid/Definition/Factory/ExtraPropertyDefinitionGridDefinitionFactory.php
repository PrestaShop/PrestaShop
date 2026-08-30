<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Definition\Factory;

use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ExtraPropertyScopeChoiceProvider;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\ExtraPropertyTypeChoiceProvider;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\BulkActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\GridActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\ModuleExtraPropertyDefinitionAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\NonModuleExtraPropertyDefinitionAccessibilityChecker;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\RowActionCollection;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\Type\LinkRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Type\SimpleGridAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BooleanColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\BulkActionColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\FilterCollection;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Builds the grid definition for the BO extra property definition management page.
 *
 * The grid lists all entries in the extra_property_definition registry table.
 * display_front is shown as a read-only BooleanColumn — it is only editable via the form.
 * Edit and Delete actions are hidden for module-owned rows (module_name IS NOT NULL) — those
 * rows only get the read-only "view" action instead, so the grid always links to the right
 * action for a row, with no redirect needed.
 */
final class ExtraPropertyDefinitionGridDefinitionFactory extends AbstractGridDefinitionFactory
{
    use BulkDeleteActionTrait;
    use DeleteActionTrait;

    public const GRID_ID = 'extra_property_definition';

    public function __construct(
        HookDispatcherInterface $hookDispatcher,
        private readonly NonModuleExtraPropertyDefinitionAccessibilityChecker $nonModuleAccessibilityChecker,
        private readonly ModuleExtraPropertyDefinitionAccessibilityChecker $moduleOwnedAccessibilityChecker,
        private readonly ExtraPropertyTypeChoiceProvider $typeChoiceProvider,
        private readonly ExtraPropertyScopeChoiceProvider $scopeChoiceProvider,
    ) {
        parent::__construct($hookDispatcher);
    }

    /**
     * {@inheritdoc}
     */
    protected function getId(): string
    {
        return self::GRID_ID;
    }

    /**
     * {@inheritdoc}
     */
    protected function getName(): string
    {
        return $this->trans('Extra property definitions', [], 'Admin.Advparameters.Feature');
    }

    /**
     * {@inheritdoc}
     */
    protected function getColumns(): ColumnCollection
    {
        return (new ColumnCollection())
            ->add(
                (new BulkActionColumn('bulk_action'))
                    ->setOptions([
                        'bulk_field' => 'id_extra_property_definition',
                    ])
            )
            ->add(
                (new DataColumn('id_extra_property_definition'))
                    ->setName($this->trans('ID', [], 'Admin.Global'))
                    ->setOptions([
                        'field' => 'id_extra_property_definition',
                    ])
            )
            ->add(
                (new DataColumn('entity_name'))
                    ->setName($this->trans('Entity', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'entity_name',
                    ])
            )
            ->add(
                (new DataColumn('module_name'))
                    ->setName($this->trans('Module', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'module_name',
                    ])
            )
            ->add(
                (new DataColumn('property_name'))
                    ->setName($this->trans('Property name', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'property_name',
                    ])
            )
            ->add(
                (new DataColumn('type'))
                    ->setName($this->trans('Type', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'type',
                    ])
            )
            ->add(
                (new DataColumn('scope'))
                    ->setName($this->trans('Scope', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'scope',
                    ])
            )
            ->add(
                (new BooleanColumn('display_front'))
                    ->setName($this->trans('Front', [], 'Admin.Advparameters.Feature'))
                    ->setOptions([
                        'field' => 'display_front',
                        'true_name' => $this->trans('Yes', [], 'Admin.Global'),
                        'false_name' => $this->trans('No', [], 'Admin.Global'),
                        'clickable' => false,
                    ])
            )
            ->add(
                (new ActionColumn('actions'))
                    ->setName($this->trans('Actions', [], 'Admin.Global'))
                    ->setOptions([
                        'actions' => (new RowActionCollection())
                            ->add(
                                (new LinkRowAction('edit'))
                                    ->setIcon('edit')
                                    ->setName($this->trans('Edit', [], 'Admin.Actions'))
                                    ->setOptions([
                                        'route' => 'admin_extra_property_definitions_edit',
                                        'route_param_name' => 'extraPropertyDefinitionId',
                                        'route_param_field' => 'id_extra_property_definition',
                                        'clickable_row' => true,
                                        'accessibility_checker' => $this->nonModuleAccessibilityChecker,
                                    ])
                            )
                            ->add(
                                (new LinkRowAction('view'))
                                    ->setIcon('visibility')
                                    ->setName($this->trans('View', [], 'Admin.Actions'))
                                    ->setOptions([
                                        'route' => 'admin_extra_property_definitions_view',
                                        'route_param_name' => 'extraPropertyDefinitionId',
                                        'route_param_field' => 'id_extra_property_definition',
                                        'clickable_row' => true,
                                        'accessibility_checker' => $this->moduleOwnedAccessibilityChecker,
                                    ])
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_extra_property_definitions_delete',
                                    'extraPropertyDefinitionId',
                                    'id_extra_property_definition',
                                    'POST',
                                    [],
                                    [
                                        'accessibility_checker' => $this->nonModuleAccessibilityChecker,
                                        'confirm_message' => $this->trans('Are you sure you want to delete the selected item(s)? The SQL column will be kept.', [], 'Admin.Advparameters.Feature'),
                                    ]
                                )
                            )
                            ->add(
                                $this->buildDeleteAction(
                                    'admin_extra_property_definitions_delete_drop_column',
                                    'extraPropertyDefinitionId',
                                    'id_extra_property_definition',
                                    'POST',
                                    [],
                                    [
                                        'accessibility_checker' => $this->nonModuleAccessibilityChecker,
                                        'confirm_message' => $this->trans('Are you sure? This will also permanently DROP the SQL column and all its data.', [], 'Admin.Advparameters.Feature'),
                                        'modal_options' => [
                                            'title' => $this->trans('Delete + drop column', [], 'Admin.Actions'),
                                            'confirm_button_label' => $this->trans('Delete + drop', [], 'Admin.Actions'),
                                        ],
                                    ],
                                    $this->trans('Delete + drop column', [], 'Admin.Advparameters.Feature'),
                                    'delete_drop_column',
                                    'delete_forever'
                                )
                            ),
                    ])
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function getFilters()
    {
        $typeChoices = $this->typeChoiceProvider->getChoices();
        $scopeChoices = $this->scopeChoiceProvider->getChoices();

        return (new FilterCollection())
            ->add(
                (new Filter('entity_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search entity', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('entity_name')
            )
            ->add(
                (new Filter('module_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search module', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('module_name')
            )
            ->add(
                (new Filter('property_name', TextType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'attr' => ['placeholder' => $this->trans('Search property', [], 'Admin.Actions')],
                    ])
                    ->setAssociatedColumn('property_name')
            )
            ->add(
                (new Filter('type', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => $typeChoices,
                        'placeholder' => $this->trans('-- Type --', [], 'Admin.Actions'),
                    ])
                    ->setAssociatedColumn('type')
            )
            ->add(
                (new Filter('scope', ChoiceType::class))
                    ->setTypeOptions([
                        'required' => false,
                        'choices' => $scopeChoices,
                        'placeholder' => $this->trans('-- Scope --', [], 'Admin.Actions'),
                    ])
                    ->setAssociatedColumn('scope')
            )
            ->add(
                (new Filter('actions', SearchAndResetType::class))
                    ->setTypeOptions([
                        'reset_route' => 'admin_common_reset_search_by_filter_id',
                        'reset_route_params' => [
                            'filterId' => self::GRID_ID,
                        ],
                        'redirect_route' => 'admin_extra_property_definitions_index',
                    ])
                    ->setAssociatedColumn('actions')
            );
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
                $this->buildBulkDeleteAction(
                    'admin_extra_property_definitions_bulk_delete',
                    [
                        'confirm_message' => $this->trans('Are you sure you want to delete the selected item(s)? The SQL columns will be kept.', [], 'Admin.Advparameters.Feature'),
                    ]
                )
            )
            ->add(
                $this->buildBulkDeleteAction(
                    'admin_extra_property_definitions_bulk_delete_drop_column',
                    [
                        'confirm_message' => $this->trans('Are you sure? This will also permanently DROP the SQL columns and all their data.', [], 'Admin.Advparameters.Feature'),
                        'modal_options' => [
                            'title' => $this->trans('Delete + drop columns', [], 'Admin.Actions'),
                            'confirm_button_label' => $this->trans('Delete + drop', [], 'Admin.Actions'),
                        ],
                    ],
                    'delete_selection_drop_column',
                    $this->trans('Delete selected + drop SQL columns', [], 'Admin.Advparameters.Feature')
                )
            );
    }
}
