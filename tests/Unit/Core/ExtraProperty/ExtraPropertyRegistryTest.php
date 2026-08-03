<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionWriterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistry;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormTypeMap;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\FormOptionsValidator;
use PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ExtraPropertySchemaManagerInterface;
use Psr\Log\NullLogger;
use RuntimeException;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;
use Throwable;

/**
 * Covers ExtraPropertyRegistry::register() change handling on already-registered
 * definitions (destructive schema changes are refused, non-destructive ones are
 * accepted — the schema manager then syncs them onto the live column, see
 * ExtraPropertySchemaManagerSyncTest), the failure contract (every hard failure
 * throws a typed core exception), the DDL-before-save ordering that keeps a failed
 * creation from persisting anything, and unregister()'s row-delete-before-column-drop
 * ordering.
 */
class ExtraPropertyRegistryTest extends TestCase
{
    public function testNewRegistrationIsAccepted(): void
    {
        $incoming = $this->definition();
        $registry = $this->buildRegistry(existing: null, expectSave: true);

        $this->assertSame(1, $registry->register($incoming));
    }

    public function testIdenticalReRegistrationIsAccepted(): void
    {
        $incoming = $this->definition();
        $registry = $this->buildRegistry(existing: $this->definition(), expectSave: true);

        $this->assertSame(1, $registry->register($incoming));
    }

    /**
     * @dataProvider destructiveChangeProvider
     */
    public function testDestructiveChangesAreRefused(ExtraPropertyDefinition $existing, ExtraPropertyDefinition $incoming): void
    {
        $registry = $this->buildRegistry(existing: $existing, expectSave: false);

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::DESTRUCTIVE_SCHEMA_CHANGE);

        $registry->register($incoming);
    }

    public static function destructiveChangeProvider(): array
    {
        return [
            'type change' => [
                self::definition(type: ExtraPropertyType::STRING),
                self::definition(type: ExtraPropertyType::INT),
            ],
            'string size decrease' => [
                self::definition(size: 255),
                self::definition(size: 64),
            ],
            'string size decrease from implicit 255' => [
                self::definition(size: null),
                self::definition(size: 100),
            ],
            'nullable tightening' => [
                self::definition(nullable: true),
                self::definition(nullable: false),
            ],
            'enum value removal' => [
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['a', 'b', 'c']),
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['a', 'b']),
            ],
            'varchar fallback to enum switch' => [
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: null),
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['a']),
            ],
            'enum to varchar fallback switch' => [
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['a']),
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: null),
            ],
        ];
    }

    /**
     * @dataProvider appliableChangeProvider
     */
    public function testNonDestructiveChangesAreAccepted(ExtraPropertyDefinition $existing, ExtraPropertyDefinition $incoming): void
    {
        $registry = $this->buildRegistry(existing: $existing, expectSave: true);

        $this->assertSame(1, $registry->register($incoming));
    }

    public static function appliableChangeProvider(): array
    {
        return [
            'defaultValue change' => [
                self::definition(defaultValue: 'old'),
                self::definition(defaultValue: 'new'),
            ],
            'defaultValue added' => [
                self::definition(defaultValue: null),
                self::definition(defaultValue: 'new'),
            ],
            'defaultValue removed' => [
                self::definition(defaultValue: 'old'),
                self::definition(defaultValue: null),
            ],
            'string size increase' => [
                self::definition(size: 64),
                self::definition(size: 255),
            ],
            'string size increase from implicit 255' => [
                self::definition(size: null),
                self::definition(size: 500),
            ],
            'nullable relaxing' => [
                self::definition(nullable: false),
                self::definition(nullable: true),
            ],
            'enum value addition' => [
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['a', 'b']),
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['a', 'b', 'c']),
            ],
            'enum reordering' => [
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['a', 'b']),
                self::definition(type: ExtraPropertyType::CHOICE, enumValues: ['b', 'a']),
            ],
        ];
    }

    public function testExplicitSize255EqualsImplicitNullSize(): void
    {
        // null and 255 are the same effective varchar length: not blocked.
        $registry = $this->buildRegistry(existing: $this->definition(size: null), expectSave: true);

        $this->assertSame(1, $registry->register($this->definition(size: 255)));
    }

    public function testScopeConflictWithAnotherScopeIsRefused(): void
    {
        // Same (entity, module, property) registered under another scope: refused before any write.
        $registry = $this->buildRegistry(existing: $this->definition(scope: ExtraPropertyScope::SHOP), expectSave: false);

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::SCOPE_CONFLICT);

        $registry->register($this->definition(scope: ExtraPropertyScope::COMMON));
    }

    public function testInvalidFormOptionsAreRejectedBeforeSave(): void
    {
        $registry = $this->buildRegistry(existing: null, expectSave: false);

        try {
            $registry->register($this->definition(formOptions: ['not_a_real_option' => true]));
            $this->fail('An ExtraPropertyRegistryException should have been thrown.');
        } catch (ExtraPropertyRegistryException $exception) {
            $this->assertSame(ExtraPropertyRegistryException::INVALID_FORM_OPTIONS, $exception->getCode());
            $this->assertStringContainsString('not_a_real_option', $exception->getMessage());
            // The individual validation errors stay available as a list for form consumers.
            $this->assertNotSame([], $exception->getErrors());
            $this->assertStringContainsString('not_a_real_option', $exception->getErrors()[0]);
        }
    }

    public function testInvalidFormTypeIsRejectedBeforeSave(): void
    {
        $registry = $this->buildRegistry(existing: null, expectSave: false);

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::INVALID_FORM_OPTIONS);
        $this->expectExceptionMessage('is not a Symfony form type');

        $registry->register($this->definition(formType: 'Vendor\Unknown\FancyType'));
    }

    public function testValidFormOptionsAreAccepted(): void
    {
        $registry = $this->buildRegistry(existing: null, expectSave: true);

        $this->assertSame(1, $registry->register($this->definition(formOptions: ['attr' => ['class' => 'custom-class']])));
    }

    public function testDdlRunsBeforeSave(): void
    {
        $ddlDone = false;

        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->expects($this->once())
            ->method('ensureExtraTableAndColumn')
            ->willReturnCallback(function () use (&$ddlDone): bool {
                $ddlDone = true;

                return true;
            });

        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->expects($this->once())
            ->method('save')
            ->willReturnCallback(function () use (&$ddlDone): int {
                $this->assertTrue($ddlDone, 'save() must only run after the DDL succeeded');

                return 1;
            });

        $registry = $this->createRegistry(existing: null, writeRepository: $writeRepository, schemaManager: $schemaManager);

        $this->assertSame(1, $registry->register($this->definition()));
    }

    /**
     * The reported orphan-definition bug, pinned at unit level: a schema failure on a
     * CREATION must escape before any row is persisted.
     */
    public function testSchemaFailureOnCreationNeverSaves(): void
    {
        $registry = $this->createRegistry(
            existing: null,
            writeRepository: $this->neverSavingWriter(),
            schemaManager: $this->throwingSchemaManager(new ExtraPropertyRegistryException('The base table "ps_demo" does not exist.', ExtraPropertyRegistryException::BASE_TABLE_NOT_FOUND)),
        );

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::BASE_TABLE_NOT_FOUND);

        $registry->register($this->definition());
    }

    public function testSchemaFailureOnUpdateNeverSaves(): void
    {
        $registry = $this->createRegistry(
            existing: $this->definition(),
            writeRepository: $this->neverSavingWriter(),
            schemaManager: $this->throwingSchemaManager(new ExtraPropertyRegistryException('The base table "ps_demo" does not exist.', ExtraPropertyRegistryException::BASE_TABLE_NOT_FOUND)),
        );

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::BASE_TABLE_NOT_FOUND);

        $registry->register($this->definition());
    }

    public function testUnexpectedSchemaFailureIsWrappedWithPrevious(): void
    {
        $driverFailure = new RuntimeException('server has gone away');
        $registry = $this->createRegistry(
            existing: null,
            writeRepository: $this->neverSavingWriter(),
            schemaManager: $this->throwingSchemaManager($driverFailure),
        );

        try {
            $registry->register($this->definition());
            $this->fail('An ExtraPropertyRegistryException should have been thrown.');
        } catch (ExtraPropertyRegistryException $exception) {
            $this->assertSame(ExtraPropertyRegistryException::SCHEMA_FAILURE, $exception->getCode());
            $this->assertSame($driverFailure, $exception->getPrevious());
            $this->assertStringContainsString('server has gone away', $exception->getMessage());
        }
    }

    public function testSaveFailureOnCreationDropsTheColumnItAdded(): void
    {
        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->method('ensureExtraTableAndColumn')->willReturn(true);
        $schemaManager->expects($this->once())->method('dropExtraColumnIfExists');

        $registry = $this->createRegistry(existing: null, writeRepository: $this->failingWriter(), schemaManager: $schemaManager);

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::PERSISTENCE_FAILURE);

        $registry->register($this->definition());
    }

    public function testSaveFailureOnCreationKeepsAPreExistingColumn(): void
    {
        // The column existed before this call (ensure... returned false): a leftover of
        // unregister(dropColumn: false) may still hold data, it must never be dropped.
        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->method('ensureExtraTableAndColumn')->willReturn(false);
        $schemaManager->expects($this->never())->method('dropExtraColumnIfExists');

        $registry = $this->createRegistry(existing: null, writeRepository: $this->failingWriter(), schemaManager: $schemaManager);

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::PERSISTENCE_FAILURE);

        $registry->register($this->definition());
    }

    public function testSaveFailureOnUpdateLeavesTheColumnInPlace(): void
    {
        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->method('ensureExtraTableAndColumn')->willReturn(true);
        $schemaManager->expects($this->never())->method('dropExtraColumnIfExists');

        $registry = $this->createRegistry(existing: $this->definition(), writeRepository: $this->failingWriter(), schemaManager: $schemaManager);

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::PERSISTENCE_FAILURE);

        $registry->register($this->definition());
    }

    public function testFailingCleanupStillThrowsThePersistenceFailure(): void
    {
        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->method('ensureExtraTableAndColumn')->willReturn(true);
        $schemaManager->expects($this->once())
            ->method('dropExtraColumnIfExists')
            ->willThrowException(new RuntimeException('drop failed too'));

        $registry = $this->createRegistry(existing: null, writeRepository: $this->failingWriter(), schemaManager: $schemaManager);

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::PERSISTENCE_FAILURE);

        $registry->register($this->definition());
    }

    public function testUnregisterUnknownDefinitionIsANoOp(): void
    {
        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->expects($this->never())->method('deleteByDefinition');

        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->expects($this->never())->method('dropExtraColumnIfExists');

        $registry = $this->createRegistry(existing: null, writeRepository: $writeRepository, schemaManager: $schemaManager);

        $registry->unregister($this->definition(), true);
    }

    public function testUnregisterDeletesTheRowBeforeDroppingTheColumn(): void
    {
        $rowDeleted = false;

        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->expects($this->once())
            ->method('deleteByDefinition')
            ->willReturnCallback(function () use (&$rowDeleted): bool {
                $rowDeleted = true;

                return true;
            });

        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->expects($this->once())
            ->method('dropExtraColumnIfExists')
            ->willReturnCallback(function () use (&$rowDeleted): void {
                $this->assertTrue($rowDeleted, 'the column drop must only run after the definition row is deleted');
            });

        $registry = $this->createRegistry(existing: $this->definition(), writeRepository: $writeRepository, schemaManager: $schemaManager);

        $registry->unregister($this->definition(), true);
    }

    public function testUnregisterRowDeleteFailureThrowsAndSkipsTheDrop(): void
    {
        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->method('deleteByDefinition')->willReturn(false);

        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->expects($this->never())->method('dropExtraColumnIfExists');

        $registry = $this->createRegistry(existing: $this->definition(), writeRepository: $writeRepository, schemaManager: $schemaManager);

        $this->expectException(ExtraPropertyRegistryException::class);
        $this->expectExceptionCode(ExtraPropertyRegistryException::PERSISTENCE_FAILURE);

        $registry->unregister($this->definition(), true);
    }

    public function testUnregisterDropFailureThrowsSchemaExceptionWithTheRowAlreadyDeleted(): void
    {
        $dropFailure = new RuntimeException('lock wait timeout');

        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->expects($this->once())->method('deleteByDefinition')->willReturn(true);

        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->method('dropExtraColumnIfExists')->willThrowException($dropFailure);

        $registry = $this->createRegistry(existing: $this->definition(), writeRepository: $writeRepository, schemaManager: $schemaManager);

        try {
            $registry->unregister($this->definition(), true);
            $this->fail('An ExtraPropertyRegistryException should have been thrown.');
        } catch (ExtraPropertyRegistryException $exception) {
            $this->assertSame(ExtraPropertyRegistryException::SCHEMA_FAILURE, $exception->getCode());
            $this->assertSame($dropFailure, $exception->getPrevious());
            $this->assertStringContainsString('left in place', $exception->getMessage());
        }
    }

    public function testUnregisterWithoutDropColumnNeverTouchesTheSchema(): void
    {
        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->expects($this->once())->method('deleteByDefinition')->willReturn(true);

        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->expects($this->never())->method('dropExtraColumnIfExists');

        $registry = $this->createRegistry(existing: $this->definition(), writeRepository: $writeRepository, schemaManager: $schemaManager);

        $registry->unregister($this->definition(), false);
    }

    private function buildRegistry(
        ?ExtraPropertyDefinition $existing,
        bool $expectSave,
    ): ExtraPropertyRegistry {
        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->expects($expectSave ? $this->once() : $this->never())
            ->method('save')
            ->willReturn(1);

        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->expects($expectSave ? $this->once() : $this->never())
            ->method('ensureExtraTableAndColumn')
            ->willReturn(true);

        return $this->createRegistry($existing, $writeRepository, $schemaManager);
    }

    private function createRegistry(
        ?ExtraPropertyDefinition $existing,
        ExtraPropertyDefinitionWriterInterface&MockObject $writeRepository,
        ExtraPropertySchemaManagerInterface&MockObject $schemaManager,
    ): ExtraPropertyRegistry {
        $readRepository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $readRepository->method('findDefinitionByModuleAndField')->willReturn($existing);

        return new ExtraPropertyRegistry(
            $readRepository,
            $writeRepository,
            $schemaManager,
            new NullLogger(),
            new FormOptionsValidator(
                Forms::createFormFactoryBuilder()
                    // The validator's option merge carries 'constraints', defined by this extension.
                    ->addExtension(new ValidatorExtension(Validation::createValidator()))
                    ->getFormFactory(),
                new ExtraPropertyFormTypeMap()
            ),
        );
    }

    private function neverSavingWriter(): ExtraPropertyDefinitionWriterInterface&MockObject
    {
        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->expects($this->never())->method('save');

        return $writeRepository;
    }

    private function failingWriter(): ExtraPropertyDefinitionWriterInterface&MockObject
    {
        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->expects($this->once())->method('save')->willReturn(false);

        return $writeRepository;
    }

    private function throwingSchemaManager(Throwable $failure): ExtraPropertySchemaManagerInterface&MockObject
    {
        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->expects($this->once())
            ->method('ensureExtraTableAndColumn')
            ->willThrowException($failure);
        $schemaManager->expects($this->never())->method('dropExtraColumnIfExists');

        return $schemaManager;
    }

    private static function definition(
        ExtraPropertyType $type = ExtraPropertyType::STRING,
        ExtraPropertyScope $scope = ExtraPropertyScope::COMMON,
        ?int $size = null,
        bool $nullable = true,
        ?array $enumValues = null,
        int|float|string|bool|null $defaultValue = null,
        ?string $formType = null,
        ?array $formOptions = null,
    ): ExtraPropertyDefinition {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: 'test_field',
            type: $type,
            scope: $scope,
            moduleName: 'mymodule',
            enumValues: $enumValues,
            defaultValue: $defaultValue,
            nullable: $nullable,
            size: $size,
            formType: $formType,
            formOptions: $formOptions,
        );
    }
}
