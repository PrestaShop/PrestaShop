<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command;

use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject\ExtraPropertyDefinitionId;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use Symfony\Component\Validator\Constraint;

/**
 * Updates editable metadata for a core extra property definition.
 *
 * Structural fields (entity_name, property_name, type, scope) are intentionally absent —
 * changing them implies moving storage tables / converting data, which is not supported
 * without unregister + register.
 *
 * nullable, size, enumValues and sqlIndex ARE editable, but only in the non-destructive
 * direction (relaxing nullable, increasing size, adding enum values, changing the index
 * strategy) — the registry (ExtraPropertyRegistry::hasStorageChanges()) refuses any
 * destructive attempt and the write simply fails.
 *
 * Use a builder pattern: construct with the id, then call setters as needed.
 */
class UpdateExtraPropertyDefinitionCommand
{
    /**
     * @var ExtraPropertyDefinitionId
     */
    protected ExtraPropertyDefinitionId $id;

    /**
     * @var bool|null
     */
    protected ?bool $displayFront = null;

    /**
     * @var bool|null
     */
    protected ?bool $required = null;

    /**
     * @var bool|null
     */
    protected ?bool $nullable = null;

    /**
     * @var int|null
     */
    protected ?int $size = null;

    /**
     * @var list<string>|null
     */
    protected ?array $enumValues = null;

    /**
     * @var ExtraPropertySqlIndex|null
     */
    protected ?ExtraPropertySqlIndex $sqlIndex = null;

    /**
     * @var string|null
     */
    protected ?string $labelWording = null;

    /**
     * @var string|null
     */
    protected ?string $labelDomain = null;

    /**
     * @var string|null
     */
    protected ?string $descriptionWording = null;

    /**
     * @var string|null
     */
    protected ?string $descriptionDomain = null;

    /**
     * @var list<Constraint>|null
     */
    protected ?array $constraints = null;

    /**
     * @var string|null
     */
    protected ?string $formFieldType = null;

    /**
     * @var array<string, mixed>|null
     */
    protected ?array $formOptions = null;

    /**
     * @var list<string>|null
     */
    protected ?array $associatedForms = null;

    /**
     * @var list<string>|null
     */
    protected ?array $associatedGrids = null;

    /**
     * @var list<string>|null
     */
    protected ?array $associatedApis = null;

    /**
     * @param int $id
     */
    public function __construct(int $id)
    {
        $this->id = new ExtraPropertyDefinitionId($id);
    }

    /**
     * @return ExtraPropertyDefinitionId
     */
    public function getId(): ExtraPropertyDefinitionId
    {
        return $this->id;
    }

    /**
     * @return bool|null
     */
    public function getDisplayFront(): ?bool
    {
        return $this->displayFront;
    }

    /**
     * @param bool $displayFront
     *
     * @return self
     */
    public function setDisplayFront(bool $displayFront): self
    {
        $this->displayFront = $displayFront;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getRequired(): ?bool
    {
        return $this->required;
    }

    /**
     * @param bool $required
     *
     * @return self
     */
    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getNullable(): ?bool
    {
        return $this->nullable;
    }

    /**
     * @param bool $nullable
     *
     * @return self
     */
    public function setNullable(bool $nullable): self
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getSize(): ?int
    {
        return $this->size;
    }

    /**
     * @param int $size
     *
     * @return self
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return list<string>|null
     */
    public function getEnumValues(): ?array
    {
        return $this->enumValues;
    }

    /**
     * @param list<string>|null $enumValues
     *
     * @return self
     */
    public function setEnumValues(?array $enumValues): self
    {
        $this->enumValues = $enumValues;

        return $this;
    }

    /**
     * @return ExtraPropertySqlIndex|null
     */
    public function getSqlIndex(): ?ExtraPropertySqlIndex
    {
        return $this->sqlIndex;
    }

    /**
     * @param ExtraPropertySqlIndex $sqlIndex
     *
     * @return self
     */
    public function setSqlIndex(ExtraPropertySqlIndex $sqlIndex): self
    {
        $this->sqlIndex = $sqlIndex;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabelWording(): ?string
    {
        return $this->labelWording;
    }

    /**
     * @param string|null $labelWording
     *
     * @return self
     */
    public function setLabelWording(?string $labelWording): self
    {
        $this->labelWording = $labelWording;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLabelDomain(): ?string
    {
        return $this->labelDomain;
    }

    /**
     * @param string|null $labelDomain
     *
     * @return self
     */
    public function setLabelDomain(?string $labelDomain): self
    {
        $this->labelDomain = $labelDomain;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptionWording(): ?string
    {
        return $this->descriptionWording;
    }

    /**
     * @param string|null $descriptionWording
     *
     * @return self
     */
    public function setDescriptionWording(?string $descriptionWording): self
    {
        $this->descriptionWording = $descriptionWording;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescriptionDomain(): ?string
    {
        return $this->descriptionDomain;
    }

    /**
     * @param string|null $descriptionDomain
     *
     * @return self
     */
    public function setDescriptionDomain(?string $descriptionDomain): self
    {
        $this->descriptionDomain = $descriptionDomain;

        return $this;
    }

    /**
     * @return list<Constraint>|null
     */
    public function getConstraints(): ?array
    {
        return $this->constraints;
    }

    /**
     * @param list<Constraint>|null $constraints
     *
     * @return self
     */
    public function setConstraints(?array $constraints): self
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormFieldType(): ?string
    {
        return $this->formFieldType;
    }

    /**
     * @param string|null $formFieldType
     *
     * @return self
     */
    public function setFormFieldType(?string $formFieldType): self
    {
        $this->formFieldType = $formFieldType;

        return $this;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getFormOptions(): ?array
    {
        return $this->formOptions;
    }

    /**
     * @param array<string, mixed>|null $formOptions
     *
     * @return self
     */
    public function setFormOptions(?array $formOptions): self
    {
        $this->formOptions = $formOptions;

        return $this;
    }

    /**
     * @return list<string>|null
     */
    public function getAssociatedForms(): ?array
    {
        return $this->associatedForms;
    }

    /**
     * @param list<string>|null $associatedForms
     *
     * @return self
     */
    public function setAssociatedForms(?array $associatedForms): self
    {
        $this->associatedForms = $associatedForms;

        return $this;
    }

    /**
     * @return list<string>|null
     */
    public function getAssociatedGrids(): ?array
    {
        return $this->associatedGrids;
    }

    /**
     * @param list<string>|null $associatedGrids
     *
     * @return self
     */
    public function setAssociatedGrids(?array $associatedGrids): self
    {
        $this->associatedGrids = $associatedGrids;

        return $this;
    }

    /**
     * @return list<string>|null
     */
    public function getAssociatedApis(): ?array
    {
        return $this->associatedApis;
    }

    /**
     * @param list<string>|null $associatedApis
     *
     * @return self
     */
    public function setAssociatedApis(?array $associatedApis): self
    {
        $this->associatedApis = $associatedApis;

        return $this;
    }
}
