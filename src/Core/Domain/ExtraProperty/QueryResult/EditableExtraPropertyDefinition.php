<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryResult;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use Symfony\Component\Validator\Constraint;

/**
 * Read-only DTO carrying all data for an extra property definition edit form.
 *
 * Structural fields (entity_name, property_name, type, scope) are included for display
 * purposes in the edit form (shown as read-only / disabled) — they cannot be changed without
 * unregister + register. nullable, size, enumValues and sqlIndex are included too but ARE
 * editable (non-destructively) via UpdateExtraPropertyDefinitionCommand.
 */
class EditableExtraPropertyDefinition
{
    /**
     * @param int $id
     * @param string $entityName
     * @param string|null $moduleName Null for core fields; non-null = module-owned (read-only)
     * @param string $propertyName
     * @param ExtraPropertyType $fieldType
     * @param ExtraPropertyScope $fieldScope
     * @param ExtraPropertySqlIndex $sqlIndex
     * @param bool $nullable
     * @param int|null $size Varchar size for string fields
     * @param string|null $defaultValue
     * @param list<string>|null $enumValues Allowed values for CHOICE type
     * @param bool $displayFront
     * @param bool $required
     * @param string|null $labelWording
     * @param string|null $labelDomain
     * @param string|null $descriptionWording
     * @param string|null $descriptionDomain
     * @param list<Constraint>|null $constraints
     * @param string|null $formFieldType
     * @param array<string, mixed>|null $formOptions
     * @param list<string>|null $associatedForms
     * @param list<string>|null $associatedGrids
     * @param list<string>|null $associatedApis
     */
    public function __construct(
        protected readonly int $id,
        protected readonly string $entityName,
        protected readonly ?string $moduleName,
        protected readonly string $propertyName,
        protected readonly ExtraPropertyType $fieldType,
        protected readonly ExtraPropertyScope $fieldScope,
        protected readonly ExtraPropertySqlIndex $sqlIndex,
        protected readonly bool $nullable,
        protected readonly ?int $size,
        protected readonly ?string $defaultValue,
        protected readonly ?array $enumValues,
        protected readonly bool $displayFront,
        protected readonly bool $required,
        protected readonly ?string $labelWording,
        protected readonly ?string $labelDomain,
        protected readonly ?string $descriptionWording,
        protected readonly ?string $descriptionDomain,
        protected readonly ?array $constraints,
        protected readonly ?string $formFieldType,
        protected readonly ?array $formOptions,
        protected readonly ?array $associatedForms,
        protected readonly ?array $associatedGrids,
        protected readonly ?array $associatedApis,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * Returns null for core fields. Non-null means the definition is module-owned (read-only).
     */
    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    public function getPropertyName(): string
    {
        return $this->propertyName;
    }

    public function getFieldType(): ExtraPropertyType
    {
        return $this->fieldType;
    }

    public function getFieldScope(): ExtraPropertyScope
    {
        return $this->fieldScope;
    }

    public function getSqlIndex(): ExtraPropertySqlIndex
    {
        return $this->sqlIndex;
    }

    public function isNullable(): bool
    {
        return $this->nullable;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function getDefaultValue(): ?string
    {
        return $this->defaultValue;
    }

    /**
     * @return list<string>|null
     */
    public function getEnumValues(): ?array
    {
        return $this->enumValues;
    }

    public function isDisplayFront(): bool
    {
        return $this->displayFront;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function getLabelWording(): ?string
    {
        return $this->labelWording;
    }

    public function getLabelDomain(): ?string
    {
        return $this->labelDomain;
    }

    public function getDescriptionWording(): ?string
    {
        return $this->descriptionWording;
    }

    public function getDescriptionDomain(): ?string
    {
        return $this->descriptionDomain;
    }

    /**
     * @return list<Constraint>|null
     */
    public function getConstraints(): ?array
    {
        return $this->constraints;
    }

    public function getFormFieldType(): ?string
    {
        return $this->formFieldType;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getFormOptions(): ?array
    {
        return $this->formOptions;
    }

    /**
     * @return list<string>|null
     */
    public function getAssociatedForms(): ?array
    {
        return $this->associatedForms;
    }

    /**
     * @return list<string>|null
     */
    public function getAssociatedGrids(): ?array
    {
        return $this->associatedGrids;
    }

    /**
     * @return list<string>|null
     */
    public function getAssociatedApis(): ?array
    {
        return $this->associatedApis;
    }

    /**
     * Returns true when the definition is owned by a module and cannot be modified via the BO UI.
     */
    public function isModuleOwned(): bool
    {
        return null !== $this->moduleName;
    }
}
