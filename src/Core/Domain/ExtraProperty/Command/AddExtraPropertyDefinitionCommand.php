<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command;

use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use Symfony\Component\Validator\Constraint;

/**
 * Registers a new core extra property definition (module_name = null).
 *
 * Structural fields (entity_name, property_name, type, scope, size, sql_index) are immutable
 * once created. Only label, display, and validation metadata can be changed afterwards via
 * UpdateExtraPropertyDefinitionCommand.
 */
class AddExtraPropertyDefinitionCommand
{
    /**
     * @param string $entityName Entity table name (e.g. 'product', 'customer')
     * @param string $propertyName Property identifier (e.g. 'internal_code')
     * @param ExtraPropertyType $fieldType
     * @param ExtraPropertyScope $fieldScope
     * @param ExtraPropertySqlIndex $sqlIndex
     * @param bool $displayFront Whether to include in FO presenters
     * @param bool $required Whether the field is marked required in the BO form and in the Admin API (OpenAPI) schema
     * @param bool $nullable Whether the storage column allows NULL
     * @param int|null $size Varchar size for string type (null → 255)
     * @param string|null $defaultValue SQL DEFAULT clause value
     * @param list<string>|null $enumValues Allowed values for CHOICE type
     * @param string|null $labelWording i18n wording for the BO label (required when associated_forms or associated_grids)
     * @param string|null $labelDomain Translation domain for the label
     * @param string|null $descriptionWording i18n wording for the BO description
     * @param string|null $descriptionDomain Translation domain for the description
     * @param list<Constraint>|null $constraints Symfony validation constraints applied to each value before persistence
     * @param string|null $formFieldType Symfony form type FQCN override
     * @param array<string, mixed>|null $formOptions Extra options for the Symfony form type
     * @param list<string>|null $associatedForms Form placement entries (e.g. "product:reference:after")
     * @param list<string>|null $associatedGrids Grid placement entries (e.g. "product:reference:after")
     * @param list<string>|null $associatedApis Admin API placement entries (e.g. "/products:GET")
     */
    public function __construct(
        protected readonly string $entityName,
        protected readonly string $propertyName,
        protected readonly ExtraPropertyType $fieldType = ExtraPropertyType::STRING,
        protected readonly ExtraPropertyScope $fieldScope = ExtraPropertyScope::COMMON,
        protected readonly ExtraPropertySqlIndex $sqlIndex = ExtraPropertySqlIndex::NONE,
        protected readonly bool $displayFront = false,
        protected readonly bool $required = false,
        protected readonly bool $nullable = true,
        protected readonly ?int $size = null,
        protected readonly ?string $defaultValue = null,
        protected readonly ?array $enumValues = null,
        protected readonly ?string $labelWording = null,
        protected readonly ?string $labelDomain = null,
        protected readonly ?string $descriptionWording = null,
        protected readonly ?string $descriptionDomain = null,
        protected readonly ?array $constraints = null,
        protected readonly ?string $formFieldType = null,
        protected readonly ?array $formOptions = null,
        protected readonly ?array $associatedForms = null,
        protected readonly ?array $associatedGrids = null,
        protected readonly ?array $associatedApis = null,
    ) {
    }

    public function getEntityName(): string
    {
        return $this->entityName;
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

    public function isDisplayFront(): bool
    {
        return $this->displayFront;
    }

    public function isRequired(): bool
    {
        return $this->required;
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
}
