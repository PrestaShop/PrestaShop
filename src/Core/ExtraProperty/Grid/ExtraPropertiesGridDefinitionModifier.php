<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Grid;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnInterface;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DataColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\DateTimeColumn;
use PrestaShop\PrestaShop\Core\Grid\Column\Type\Common\ToggleColumn;
use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinition;
use PrestaShop\PrestaShop\Core\Grid\Exception\ColumnNotFoundException;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShopBundle\Form\Admin\Type\YesAndNoChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Adds extra properties columns and filters into BO Symfony grids.
 */
class ExtraPropertiesGridDefinitionModifier
{
    public function __construct(
        protected readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        protected readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param GridDefinition $definition
     * @param string $gridId Grid identifier (usually equals entity table name, e.g. "product")
     */
    public function apply(GridDefinition $definition, string $gridId): void
    {
        $definitions = $this->repository->getAllDefinitions()->filterByGrid($gridId);
        if ($definitions->isEmpty()) {
            return;
        }

        $columns = $definition->getColumns();
        $filters = $definition->getFilters();

        foreach ($definitions as $extraDefinition) {
            // H8: JSON fields have no meaningful grid representation — skip them.
            if (ExtraPropertyType::JSON === $extraDefinition->getType()) {
                continue;
            }

            $columnId = $extraDefinition->getFieldName();
            if ($this->hasColumnId($columns, $columnId)) {
                continue;
            }

            $label = $this->translateLabel(
                $extraDefinition->getLabelWording(),
                $extraDefinition->getLabelDomain(),
            );

            $column = $this->buildColumn($label, $extraDefinition);

            // getGridEntry() returns the already-parsed array — no need to call parseGridEntry().
            $gridEntry = $extraDefinition->getGridEntry($gridId) ?? ['gridId' => $gridId, 'columnId' => null, 'mode' => null];
            $columnRef = $gridEntry['columnId'];
            if (null !== $columnRef) {
                try {
                    if ('before' === $gridEntry['mode']) {
                        $columns->addBefore($columnRef, $column);
                    } else {
                        $columns->addAfter($columnRef, $column);
                    }
                } catch (ColumnNotFoundException) {
                    $this->addBeforeActionsOrAtEnd($columns, $column);
                }
            } else {
                $this->addBeforeActionsOrAtEnd($columns, $column);
            }

            $filters->add(
                (new Filter($columnId, $this->resolveFilterType($extraDefinition)))
                    ->setAssociatedColumn($columnId)
                    ->setTypeOptions(['required' => false])
            );
        }
    }

    protected function buildColumn(string $label, ExtraPropertyDefinition $definition): ColumnInterface
    {
        // H8: column type is derived from the logical field type, not the form type override.
        $columnId = $definition->getFieldName();
        $moduleName = $definition->getNormalizedModuleKey();
        $fieldName = $definition->getPropertyName();

        if (ExtraPropertyType::BOOL === $definition->getType()) {
            $primaryField = $definition->getPrimaryKeyName();
            $entityName = $definition->getEntityName();

            return (new ToggleColumn($columnId))
                ->setName($label)
                ->setOptions([
                    'field' => $columnId,
                    'primary_field' => $primaryField,
                    'route' => 'admin_common_extra_properties_toggle',
                    'route_param_name' => 'entityId',
                    'extra_route_params' => [
                        // No scope param: (entity, module, property) identifies the definition
                        // uniquely across scopes; the endpoint resolves the stored scope itself.
                        'entityName' => $entityName,
                        'moduleName' => $moduleName,
                        'propertyName' => $fieldName,
                        // No shopId param: the endpoint resolves the shop constraint server-side
                        // from ShopContext, never from a client-supplied value.
                        // _legacy_controller is intentionally absent: the toggle endpoint derives
                        // the permission subject server-side from entityName (non-forgeable URL
                        // path parameter). Sending it from the client would allow privilege
                        // escalation by forging a controller name the user holds rights on.
                    ],
                ]);
        }

        if (ExtraPropertyType::DATE === $definition->getType()) {
            return (new DateTimeColumn($columnId))
                ->setName($label)
                ->setOptions([
                    'field' => $columnId,
                    'sortable' => true,
                    'clickable' => false,
                ]);
        }

        return (new DataColumn($columnId))
            ->setName($label)
            ->setOptions([
                'field' => $columnId,
                'sortable' => true,
                'clickable' => false,
            ]);
    }

    /**
     * @return class-string
     */
    protected function resolveFilterType(ExtraPropertyDefinition $definition): string
    {
        if (ExtraPropertyType::BOOL === $definition->getType()) {
            return YesAndNoChoiceType::class;
        }

        return TextType::class;
    }

    protected function addBeforeActionsOrAtEnd(ColumnCollectionInterface $columns, ColumnInterface $column): void
    {
        if ($this->hasColumnId($columns, 'actions')) {
            $columns->addBefore('actions', $column);

            return;
        }

        $columns->add($column);
    }

    protected function hasColumnId(ColumnCollectionInterface $columns, string $id): bool
    {
        foreach ($columns as $column) {
            if ($column instanceof ColumnInterface && $id === $column->getId()) {
                return true;
            }
        }

        return false;
    }

    protected function translateLabel(?string $wording, ?string $domain): string
    {
        if (null === $wording || '' === trim($wording)) {
            return '';
        }

        return $this->translator->trans($wording, [], $domain ?? 'Admin.Global');
    }
}
