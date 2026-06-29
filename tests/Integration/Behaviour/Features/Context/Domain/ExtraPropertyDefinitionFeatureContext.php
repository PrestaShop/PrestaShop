<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Exception;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\AddExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\BulkDeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\DeleteExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Command\UpdateExtraPropertyDefinitionCommand;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\BulkExtraPropertyException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyDefinitionNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ExtraPropertyRegistrationFailureException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Exception\ProtectedModuleExtraPropertyDefinitionException;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\Query\GetExtraPropertyDefinitionForEditing;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\QueryResult\EditableExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\Domain\ExtraProperty\ValueObject\ExtraPropertyDefinitionId;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertySqlIndex;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;
use Tests\Integration\Behaviour\Features\Context\Util\NoExceptionAlthoughExpectedException;
use Tests\Resources\DatabaseDump;

/**
 * Covers the BO extra property definition registry management: AddExtraPropertyDefinitionCommand,
 * UpdateExtraPropertyDefinitionCommand, DeleteExtraPropertyDefinitionCommand,
 * BulkDeleteExtraPropertyDefinitionCommand and GetExtraPropertyDefinitionForEditing.
 */
class ExtraPropertyDefinitionFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @BeforeFeature @restore-extra-property-definition-before-feature
     */
    public static function restoreExtraPropertyDefinitionTable(): void
    {
        DatabaseDump::restoreTables(['extra_property_definition']);
    }

    /**
     * All extra tables must be removed because they mess with the restore tables functions,
     * since they are scanned but have no associated table dump.
     *
     * @AfterFeature @remove-extra-tables-after-feature
     */
    public static function removeExtraTablesAfterFeature(): void
    {
        DatabaseDump::removeExtraTables();
    }

    /**
     * @When I add an extra property definition :reference with following properties:
     */
    public function addExtraPropertyDefinition(string $reference, TableNode $table): void
    {
        $data = $table->getRowsHash();

        $command = new AddExtraPropertyDefinitionCommand(
            entityName: $data['entity_name'],
            propertyName: $data['property_name'],
            fieldType: ExtraPropertyType::from($data['type'] ?? ExtraPropertyType::STRING->value),
            fieldScope: ExtraPropertyScope::from($data['scope'] ?? ExtraPropertyScope::COMMON->value),
            sqlIndex: ExtraPropertySqlIndex::from($data['sql_index'] ?? ExtraPropertySqlIndex::NONE->value),
            displayFront: filter_var($data['display_front'] ?? false, FILTER_VALIDATE_BOOL),
            required: filter_var($data['required'] ?? false, FILTER_VALIDATE_BOOL),
            nullable: filter_var($data['nullable'] ?? true, FILTER_VALIDATE_BOOL),
            size: isset($data['size']) ? (int) $data['size'] : null,
            defaultValue: $data['default_value'] ?? null,
            enumValues: isset($data['enum_values']) ? explode(',', $data['enum_values']) : null,
            labelWording: $data['label_wording'] ?? null,
            labelDomain: $data['label_domain'] ?? null,
            descriptionWording: null,
            descriptionDomain: null,
            constraints: isset($data['constraints']) ? ExtraPropertyConstraintMapper::fromNames($data['constraints']) : null,
            formFieldType: null,
            formOptions: null,
            associatedForms: isset($data['associated_forms']) ? explode(',', $data['associated_forms']) : null,
            associatedGrids: isset($data['associated_grids']) ? explode(',', $data['associated_grids']) : null,
            associatedApis: isset($data['associated_apis']) ? explode(',', $data['associated_apis']) : null,
        );

        try {
            /** @var ExtraPropertyDefinitionId $id */
            $id = $this->getCommandBus()->handle($command);
            SharedStorage::getStorage()->set($reference, $id->getValue());
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given a module-owned extra property definition :reference exists for entity :entityName named :propertyName owned by module :moduleName
     */
    public function addModuleOwnedExtraPropertyDefinition(string $reference, string $entityName, string $propertyName, string $moduleName): void
    {
        $definition = new ExtraPropertyDefinition(
            entityName: $entityName,
            propertyName: $propertyName,
            moduleName: $moduleName,
        );

        /** @var ExtraPropertyRegistryInterface $registry */
        $registry = $this->getContainer()->get(ExtraPropertyRegistryInterface::class);
        $id = $registry->register($definition);

        if (false === $id) {
            throw new RuntimeException(sprintf('Failed to set up the module-owned fixture "%s".', $reference));
        }

        SharedStorage::getStorage()->set($reference, $id);
    }

    /**
     * @Given I define an uncreated extra property definition :reference
     */
    public function defineUncreatedExtraPropertyDefinition(string $reference): void
    {
        // No row in extra_property_definition will ever have this id: used to exercise the
        // "not found" path for real (a SharedStorage lookup miss would throw a different,
        // unrelated RuntimeException before the command/query is even dispatched).
        SharedStorage::getStorage()->set($reference, 999999999);
    }

    /**
     * @When I edit extra property definition :reference with following properties:
     */
    public function editExtraPropertyDefinition(string $reference, TableNode $table): void
    {
        $command = new UpdateExtraPropertyDefinitionCommand($this->referenceToId($reference));
        $data = $table->getRowsHash();

        if (isset($data['display_front'])) {
            $command->setDisplayFront(filter_var($data['display_front'], FILTER_VALIDATE_BOOL));
        }
        if (isset($data['required'])) {
            $command->setRequired(filter_var($data['required'], FILTER_VALIDATE_BOOL));
        }
        if (isset($data['nullable'])) {
            $command->setNullable(filter_var($data['nullable'], FILTER_VALIDATE_BOOL));
        }
        if (isset($data['size'])) {
            $command->setSize((int) $data['size']);
        }
        if (isset($data['enum_values'])) {
            $command->setEnumValues(explode(',', $data['enum_values']));
        }
        if (isset($data['sql_index'])) {
            $command->setSqlIndex(ExtraPropertySqlIndex::from($data['sql_index']));
        }
        if (isset($data['label_wording'])) {
            $command->setLabelWording($data['label_wording']);
        }
        if (isset($data['constraints'])) {
            $command->setConstraints(ExtraPropertyConstraintMapper::fromNames($data['constraints']));
        }
        if (isset($data['associated_forms'])) {
            $command->setAssociatedForms(explode(',', $data['associated_forms']));
        }
        if (isset($data['associated_grids'])) {
            $command->setAssociatedGrids(explode(',', $data['associated_grids']));
        }
        if (isset($data['associated_apis'])) {
            $command->setAssociatedApis(explode(',', $data['associated_apis']));
        }

        try {
            $this->getCommandBus()->handle($command);
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I delete extra property definition :reference
     */
    public function deleteExtraPropertyDefinition(string $reference): void
    {
        $this->doDeleteExtraPropertyDefinition($reference, false);
    }

    /**
     * @When I delete extra property definition :reference and drop its column
     */
    public function deleteExtraPropertyDefinitionAndDropColumn(string $reference): void
    {
        $this->doDeleteExtraPropertyDefinition($reference, true);
    }

    private function doDeleteExtraPropertyDefinition(string $reference, bool $dropColumn): void
    {
        try {
            $this->getCommandBus()->handle(new DeleteExtraPropertyDefinitionCommand($this->referenceToId($reference), $dropColumn));
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I bulk delete extra property definitions :references
     */
    public function bulkDeleteExtraPropertyDefinitions(string $references): void
    {
        $this->doBulkDeleteExtraPropertyDefinitions($references, false);
    }

    /**
     * @When I bulk delete extra property definitions :references and drop their columns
     */
    public function bulkDeleteExtraPropertyDefinitionsAndDropColumns(string $references): void
    {
        $this->doBulkDeleteExtraPropertyDefinitions($references, true);
    }

    private function doBulkDeleteExtraPropertyDefinitions(string $references, bool $dropColumn): void
    {
        try {
            $this->getCommandBus()->handle(new BulkDeleteExtraPropertyDefinitionCommand($this->referencesToIds($references), $dropColumn));
        } catch (Exception $e) {
            $this->setLastException($e);
        }
    }

    /**
     * Single assertion step covering every parameter of a definition, in the same table format
     * as the creation/edit steps (the same table can be copy/pasted between action and
     * assertion). Only the rows present in the table are checked, so a scenario can assert on
     * just one or two fields after a partial edit.
     *
     * @Then extra property definition :reference should have the following parameters:
     */
    public function assertExtraPropertyDefinitionHasParameters(string $reference, TableNode $table): void
    {
        $definition = $this->getExtraPropertyDefinitionFromReference($reference);

        foreach ($table->getRowsHash() as $field => $expected) {
            $actual = $this->extractExtraPropertyDefinitionField($definition, $field);

            if (in_array($field, ['nullable', 'display_front', 'required'], true)) {
                $expected = filter_var($expected, FILTER_VALIDATE_BOOL) ? 'true' : 'false';
            }

            if ($actual !== $expected) {
                throw new RuntimeException(sprintf(
                    'Expected %s to be "%s" but got "%s" for extra property definition "%s".',
                    $field,
                    $expected,
                    $actual,
                    $reference
                ));
            }
        }
    }

    /**
     * Resolves one named parameter of a definition to its comma-joined / true|false string
     * representation, so it can be compared directly against a Gherkin table cell.
     */
    private function extractExtraPropertyDefinitionField(EditableExtraPropertyDefinition $definition, string $field): string
    {
        return match ($field) {
            'entity_name' => $definition->getEntityName(),
            'property_name' => $definition->getPropertyName(),
            'type' => $definition->getFieldType()->value,
            'scope' => $definition->getFieldScope()->value,
            'sql_index' => $definition->getSqlIndex()->value,
            'nullable' => $definition->isNullable() ? 'true' : 'false',
            'size' => null !== $definition->getSize() ? (string) $definition->getSize() : '',
            'default_value' => $definition->getDefaultValue() ?? '',
            'enum_values' => implode(',', $definition->getEnumValues() ?? []),
            'display_front' => $definition->isDisplayFront() ? 'true' : 'false',
            'required' => $definition->isRequired() ? 'true' : 'false',
            'label_wording' => $definition->getLabelWording() ?? '',
            'constraints' => str_replace("\n", ',', ExtraPropertyConstraintMapper::toNames($definition->getConstraints()) ?? ''),
            'associated_forms' => implode(',', $definition->getAssociatedForms() ?? []),
            'associated_grids' => implode(',', $definition->getAssociatedGrids() ?? []),
            'associated_apis' => implode(',', $definition->getAssociatedApis() ?? []),
            default => throw new RuntimeException(sprintf('Unknown extra property definition parameter "%s".', $field)),
        };
    }

    /**
     * @Then extra property definition :reference should still exist
     */
    public function assertExtraPropertyDefinitionExists(string $reference): void
    {
        if (!$this->isFoundExtraPropertyDefinition($reference)) {
            throw new NoExceptionAlthoughExpectedException(sprintf(
                'Extra property definition "%s" does not exist, but it was expected to still exist.',
                $reference
            ));
        }
    }

    /**
     * @Then extra property definition :reference should no longer exist
     */
    public function assertExtraPropertyDefinitionDoesNotExist(string $reference): void
    {
        if ($this->isFoundExtraPropertyDefinition($reference)) {
            throw new NoExceptionAlthoughExpectedException(sprintf(
                'Extra property definition "%s" exists, but it was expected to be deleted.',
                $reference
            ));
        }
    }

    /**
     * @Then I should get an error that the extra property definition was not found
     */
    public function assertLastErrorIsNotFound(): void
    {
        $this->assertLastErrorIs(ExtraPropertyDefinitionNotFoundException::class);
    }

    /**
     * @Then I should get an error that the extra property definition is protected by a module
     */
    public function assertLastErrorIsProtectedByModule(): void
    {
        $this->assertLastErrorIs(ProtectedModuleExtraPropertyDefinitionException::class);
    }

    /**
     * @Then I should get an error registering the extra property definition
     */
    public function assertLastErrorIsRegistrationFailure(): void
    {
        $this->assertLastErrorIs(ExtraPropertyRegistrationFailureException::class);
    }

    /**
     * @Then the bulk deletion should report :count skipped definitions
     */
    public function assertBulkDeletionSkippedCount(int $count): void
    {
        /** @var BulkExtraPropertyException $exception */
        $exception = $this->assertLastErrorIs(BulkExtraPropertyException::class);

        if (count($exception->getExceptions()) !== $count) {
            throw new RuntimeException(sprintf(
                'Expected %d skipped definition(s) in the bulk deletion, got %d.',
                $count,
                count($exception->getExceptions())
            ));
        }
    }

    private function getExtraPropertyDefinitionFromReference(string $reference): EditableExtraPropertyDefinition
    {
        /** @var EditableExtraPropertyDefinition $definition */
        $definition = $this->getQueryBus()->handle(new GetExtraPropertyDefinitionForEditing($this->referenceToId($reference)));

        return $definition;
    }

    private function isFoundExtraPropertyDefinition(string $reference): bool
    {
        try {
            $this->getExtraPropertyDefinitionFromReference($reference);

            return true;
        } catch (ExtraPropertyDefinitionNotFoundException) {
            return false;
        }
    }
}
