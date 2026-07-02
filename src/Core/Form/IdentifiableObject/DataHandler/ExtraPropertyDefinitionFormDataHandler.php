<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use JsonException;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\UpdateExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject\ExtraPropertyDefinitionId;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;

/**
 * Handles form data submission for extra property definitions.
 *
 * The submitted data is nested by card section (field_definition, visibility, labels,
 * validation, advanced), matching ExtraPropertyDefinitionType's sub-form structure. Each
 * section's fields are always present (Symfony Form guarantees this), so no defensive `?? ''`
 * fallback is needed for required fields — only the empty-string to null normalization (via the
 * Elvis operator) for optional string fields.
 *
 * create() dispatches AddExtraPropertyDefinitionCommand (no module_name — always null for BO-created fields).
 * update() dispatches UpdateExtraPropertyDefinitionCommand (structural fields are intentionally excluded).
 *
 * The advanced card's association textareas (associated_forms/grids/apis) are edited as
 * newline-separated plain entries (one placement entry per line); only form_options is still
 * edited as raw JSON — the one boundary where JSON decoding belongs; the CQRS commands
 * themselves only ever carry native arrays. Malformed form_options JSON throws instead of
 * being silently dropped — the form's Json constraint normally blocks it before this handler
 * runs (see ExtraPropertyDefinitionAdvancedType), so a throw here only surfaces for
 * programmatic submissions or hook-mutated form data.
 */
final class ExtraPropertyDefinitionFormDataHandler implements FormDataHandlerInterface
{
    public function __construct(protected readonly CommandBusInterface $commandBus)
    {
    }

    /**
     * {@inheritdoc}
     *
     * @param array<string, mixed> $data
     *
     * @return int
     */
    public function create(array $data): int
    {
        $fieldDefinition = $data['field_definition'];
        $visibility = $data['visibility'];
        $labels = $data['labels'];
        $validation = $data['validation'];
        $advanced = $data['advanced'];

        /** @var ExtraPropertyDefinitionId $id */
        $id = $this->commandBus->handle(new AddExtraPropertyDefinitionCommand(
            entityName: $fieldDefinition['entity_name'],
            propertyName: $fieldDefinition['property_name'],
            fieldType: ExtraPropertyType::from($fieldDefinition['type']),
            fieldScope: ExtraPropertyScope::from($fieldDefinition['scope']),
            sqlIndex: ExtraPropertySqlIndex::from($fieldDefinition['sql_index']),
            displayFront: (bool) $visibility['display_front'],
            required: (bool) $visibility['required'],
            nullable: (bool) ($fieldDefinition['nullable'] ?? true),
            size: $fieldDefinition['size'] ?: null,
            defaultValue: $fieldDefinition['default_value'] ?: null,
            enumValues: $this->parseEnumValues($fieldDefinition['enum_values'] ?? null),
            labelWording: $labels['label_wording'] ?: null,
            labelDomain: $labels['label_domain'] ?: null,
            descriptionWording: $labels['description_wording'] ?: null,
            descriptionDomain: $labels['description_domain'] ?: null,
            constraints: ExtraPropertyConstraintMapper::fromNames($validation['constraints'] ?? null),
            formType: $advanced['form_type'] ?: null,
            formOptions: $this->parseJsonObject($advanced['form_options'] ?? null),
            associatedForms: $this->parseLines($advanced['associated_forms'] ?? null),
            associatedGrids: $this->parseLines($advanced['associated_grids'] ?? null),
            associatedApis: $this->parseLines($advanced['associated_apis'] ?? null),
        ));

        return $id->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     * @param array<string, mixed> $data
     */
    public function update($id, array $data): void
    {
        $fieldDefinition = $data['field_definition'];
        $visibility = $data['visibility'];
        $labels = $data['labels'];
        $validation = $data['validation'];
        $advanced = $data['advanced'];

        $command = (new UpdateExtraPropertyDefinitionCommand((int) $id))
            ->setDisplayFront((bool) $visibility['display_front'])
            ->setRequired((bool) $visibility['required'])
            ->setNullable((bool) ($fieldDefinition['nullable'] ?? true))
            ->setSqlIndex(ExtraPropertySqlIndex::from($fieldDefinition['sql_index']))
            ->setLabelWording($labels['label_wording'] ?: null)
            ->setLabelDomain($labels['label_domain'] ?: null)
            ->setDescriptionWording($labels['description_wording'] ?: null)
            ->setDescriptionDomain($labels['description_domain'] ?: null)
            ->setConstraints(ExtraPropertyConstraintMapper::fromNames($validation['constraints'] ?? null))
            ->setFormType($advanced['form_type'] ?: null)
            ->setFormOptions($this->parseJsonObject($advanced['form_options'] ?? null))
            ->setAssociatedForms($this->parseLines($advanced['associated_forms'] ?? null))
            ->setAssociatedGrids($this->parseLines($advanced['associated_grids'] ?? null))
            ->setAssociatedApis($this->parseLines($advanced['associated_apis'] ?? null));

        if (!empty($fieldDefinition['size'])) {
            $command->setSize((int) $fieldDefinition['size']);
        }

        $enumValues = $this->parseEnumValues($fieldDefinition['enum_values'] ?? null);
        if (null !== $enumValues) {
            $command->setEnumValues($enumValues);
        }

        $this->commandBus->handle($command);
    }

    /**
     * Parses the "one value per line" enum_values textarea into a list of trimmed, non-empty strings.
     *
     * @param string|null $rawValue
     *
     * @return list<string>|null
     */
    protected function parseEnumValues(?string $rawValue): ?array
    {
        if (null === $rawValue || '' === trim($rawValue)) {
            return null;
        }

        return array_values(array_filter(array_map('trim', explode("\n", $rawValue)), static fn (string $v): bool => '' !== $v)) ?: null;
    }

    /**
     * Parses a "one placement entry per line" textarea (associated_forms/grids/apis) into a list
     * of trimmed, non-empty entries.
     *
     * A blank textarea returns [] (never null): in the update path an empty list means "clear the
     * associations" while null on the command means "leave untouched" (see
     * UpdateExtraPropertyDefinitionHandler) — so returning null here would make clearing impossible.
     *
     * @param string|null $rawValue
     *
     * @return list<string>
     */
    private function parseLines(?string $rawValue): array
    {
        if (null === $rawValue) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode("\n", $rawValue)), static fn (string $v): bool => '' !== $v));
    }

    /**
     * Decodes the JSON object submitted by the form_options textarea.
     *
     * @param string|null $rawValue
     *
     * @return array<string, mixed>|null
     *
     * @throws InvalidExtraPropertyDefinitionException when the value is not valid JSON or does not decode to an object/array
     */
    protected function parseJsonObject(?string $rawValue): ?array
    {
        if (null === $rawValue || '' === trim($rawValue)) {
            return null;
        }

        try {
            $decoded = json_decode($rawValue, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new InvalidExtraPropertyDefinitionException(
                sprintf('The form options field contains invalid JSON: %s.', $e->getMessage()),
                0,
                $e
            );
        }

        if (!is_array($decoded)) {
            throw new InvalidExtraPropertyDefinitionException(sprintf(
                'The form options field must contain a JSON object, got %s.',
                get_debug_type($decoded)
            ));
        }

        return $decoded;
    }
}
