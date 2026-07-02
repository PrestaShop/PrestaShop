<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionWriterInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistry;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\InvalidExtraPropertyFormOptionsException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\ExtraPropertyFormTypeMap;
use PrestaShop\PrestaShop\Core\ExtraProperty\Form\FormOptionsValidator;
use PrestaShop\PrestaShop\Core\ExtraProperty\Schema\ExtraPropertySchemaManagerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Forms;
use Symfony\Component\Validator\Validation;

/**
 * Covers ExtraPropertyRegistry::register() change handling on already-registered
 * definitions: destructive schema changes are refused, non-destructive ones are
 * accepted (the schema manager then syncs them onto the live column — see
 * ExtraPropertySchemaManagerSyncTest).
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

        $this->assertFalse($registry->register($incoming));
    }

    public static function destructiveChangeProvider(): array
    {
        return [
            'type change' => [
                self::definition(type: ExtraPropertyType::STRING),
                self::definition(type: ExtraPropertyType::INT),
            ],
            'scope change' => [
                self::definition(scope: ExtraPropertyScope::COMMON),
                self::definition(scope: ExtraPropertyScope::LANG),
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
        // Same (entity, module, property) registered under another scope: refused before save.
        $registry = $this->buildRegistry(existing: $this->definition(scope: ExtraPropertyScope::SHOP), expectSave: false);

        $this->assertFalse($registry->register($this->definition(scope: ExtraPropertyScope::COMMON)));
    }

    public function testInvalidFormOptionsAreRejectedBeforeSave(): void
    {
        $registry = $this->buildRegistry(existing: null, expectSave: false);

        $this->expectException(InvalidExtraPropertyFormOptionsException::class);
        $this->expectExceptionMessage('not_a_real_option');

        $registry->register($this->definition(formOptions: ['not_a_real_option' => true]));
    }

    public function testInvalidFormFieldTypeIsRejectedBeforeSave(): void
    {
        $registry = $this->buildRegistry(existing: null, expectSave: false);

        $this->expectException(InvalidExtraPropertyFormOptionsException::class);
        $this->expectExceptionMessage('is not a Symfony form type');

        $registry->register($this->definition(formFieldType: 'Vendor\Unknown\FancyType'));
    }

    public function testValidFormOptionsAreAccepted(): void
    {
        $registry = $this->buildRegistry(existing: null, expectSave: true);

        $this->assertSame(1, $registry->register($this->definition(formOptions: ['attr' => ['class' => 'custom-class']])));
    }

    private function buildRegistry(
        ?ExtraPropertyDefinition $existing,
        bool $expectSave,
    ): ExtraPropertyRegistry {
        $readRepository = $this->createMock(ExtraPropertyDefinitionRepositoryInterface::class);
        $readRepository->method('findDefinitionByModuleAndField')->willReturn($existing);

        $writeRepository = $this->createMock(ExtraPropertyDefinitionWriterInterface::class);
        $writeRepository->expects($expectSave ? $this->once() : $this->never())
            ->method('save')
            ->willReturn(1);

        $schemaManager = $this->createMock(ExtraPropertySchemaManagerInterface::class);
        $schemaManager->expects($expectSave ? $this->once() : $this->never())
            ->method('ensureExtraTableAndColumn');

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

    private static function definition(
        ExtraPropertyType $type = ExtraPropertyType::STRING,
        ExtraPropertyScope $scope = ExtraPropertyScope::COMMON,
        ?int $size = null,
        bool $nullable = true,
        ?array $enumValues = null,
        int|float|string|bool|null $defaultValue = null,
        ?string $formFieldType = null,
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
            formFieldType: $formFieldType,
            formOptions: $formOptions,
        );
    }
}
