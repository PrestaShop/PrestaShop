<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty\Value;

use Db;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyReaderInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Value\ExtraPropertyWriterInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * Default value typing (#41829), end to end against the live registry:
 *
 *  - typed defaults survive the persist → hydrate round-trip with their scalar type
 *    (a BOOL false default used to come back as "no default": naive (string) cast → ''
 *    → null on reload);
 *  - an entity with NO value row reads back the definition's default on every surface
 *    served by the reader (FO, BO entity form/grid seeds, Admin API);
 *  - JSON values written as PHP structures are stored encoded and read back decoded;
 *  - a defaultValue incompatible with the declared type is refused at registration.
 */
class ExtraPropertyDefaultValueTest extends KernelTestCase
{
    private const MODULE = 'extrapropdefaulttest';
    private const PRODUCT_ID = 1;
    private const DEFAULT_LANG_ID = 1;

    private static ExtraPropertyRegistryInterface $registry;
    private static ExtraPropertyDefinitionRepositoryInterface $definitionRepository;
    private static ExtraPropertyReaderInterface $reader;
    private static ExtraPropertyWriterInterface $writer;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        self::initServices();

        foreach (self::definitions() as $definition) {
            self::$registry->register($definition);
        }
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::definitions() as $definition) {
            self::$registry->unregister($definition, true);
        }
        DatabaseDump::restoreTables(['extra_property_definition']);
        DatabaseDump::removeExtraTables();

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        self::initServices();

        Db::getInstance()->execute('DELETE FROM `' . _DB_PREFIX_ . 'product_extra`');
    }

    public function testTypedDefaultsSurviveThePersistHydrateRoundtrip(): void
    {
        $expected = [
            'dv_bool' => false,
            'dv_int' => 0,
            'dv_float' => 1.5,
            'dv_string' => 'fallback',
            // JSON defaults stay strings on the definition (they feed the DDL clause).
            'dv_json' => '{"tier":"bronze"}',
        ];

        foreach ($expected as $propertyName => $expectedDefault) {
            $definition = self::$definitionRepository->findDefinitionByModuleAndField('product', self::MODULE, $propertyName);
            $this->assertNotNull($definition, $propertyName);
            $this->assertSame($expectedDefault, $definition->getDefaultValue(), $propertyName);
        }
    }

    public function testRowLessEntityReadsBackTheDeclaredDefaults(): void
    {
        $values = self::$reader->getExtraProperties('product', 'id_product', self::PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::allShops());

        // Typed like a stored value would be — the JSON default comes back decoded.
        $this->assertFalse($values[self::MODULE]['dv_bool']);
        $this->assertSame(0, $values[self::MODULE]['dv_int']);
        $this->assertSame(1.5, $values[self::MODULE]['dv_float']);
        $this->assertSame('fallback', $values[self::MODULE]['dv_string']);
        $this->assertSame(['tier' => 'bronze'], $values[self::MODULE]['dv_json']);
        // No declared default: null.
        $this->assertNull($values[self::MODULE]['dv_no_default']);
    }

    public function testStoredValuesStillWinOverDefaults(): void
    {
        self::$writer->writeAll('product', 'id_product', self::PRODUCT_ID, [self::MODULE => [
            'dv_bool' => true,
            'dv_int' => 42,
        ]], ShopConstraint::allShops());

        $values = self::$reader->getExtraProperties('product', 'id_product', self::PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::allShops());
        $this->assertTrue($values[self::MODULE]['dv_bool']);
        $this->assertSame(42, $values[self::MODULE]['dv_int']);
        // Untouched columns of the SAME row read their DDL default, matching the seeds.
        $this->assertSame('fallback', $values[self::MODULE]['dv_string']);
    }

    public function testJsonStructureRoundtrip(): void
    {
        $structure = ['loyalty' => ['points' => 10], 'tags' => ['a', 'b']];

        self::$writer->writeAll('product', 'id_product', self::PRODUCT_ID, [self::MODULE => [
            'dv_json' => $structure,
        ]], ShopConstraint::allShops());

        // Stored encoded…
        $stored = Db::getInstance()->getValue(sprintf(
            'SELECT `%s_dv_json` FROM `%sproduct_extra` WHERE `id_product` = %d',
            self::MODULE,
            _DB_PREFIX_,
            self::PRODUCT_ID
        ));
        $this->assertJson($stored);
        // …read back decoded.
        $values = self::$reader->getExtraProperties('product', 'id_product', self::PRODUCT_ID, self::DEFAULT_LANG_ID, ShopConstraint::allShops());
        $this->assertSame($structure, $values[self::MODULE]['dv_json']);
    }

    public function testIncompatibleDefaultValueIsRefusedAtRegistration(): void
    {
        $incompatibles = [
            [ExtraPropertyType::INT, 'not-a-number', null],
            [ExtraPropertyType::FLOAT, 'abc', null],
            [ExtraPropertyType::DATE, 'not-a-date', null],
            [ExtraPropertyType::CHOICE, 'unknown', ['a', 'b']],
            [ExtraPropertyType::JSON, '{invalid', null],
        ];

        foreach ($incompatibles as [$type, $defaultValue, $enumValues]) {
            try {
                self::$registry->register(new ExtraPropertyDefinition(
                    entityName: 'product',
                    propertyName: 'dv_incompatible',
                    type: $type,
                    scope: ExtraPropertyScope::COMMON,
                    moduleName: self::MODULE,
                    enumValues: $enumValues,
                    defaultValue: $defaultValue,
                ));
                $this->fail(sprintf('Default "%s" for type %s should have been refused.', $defaultValue, $type->value));
            } catch (ExtraPropertyRegistryException $exception) {
                $this->assertSame(ExtraPropertyRegistryException::INVALID_DEFAULT_VALUE, $exception->getCode(), $type->value);
            }
        }
    }

    private static function initServices(): void
    {
        $container = self::getContainer();
        self::$registry = $container->get(ExtraPropertyRegistryInterface::class);
        self::$definitionRepository = $container->get(ExtraPropertyDefinitionRepositoryInterface::class);
        self::$reader = $container->get(ExtraPropertyReaderInterface::class);
        self::$writer = $container->get(ExtraPropertyWriterInterface::class);
    }

    /**
     * @return ExtraPropertyDefinition[]
     */
    private static function definitions(): array
    {
        return [
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'dv_bool', type: ExtraPropertyType::BOOL, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE, defaultValue: false, nullable: false),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'dv_int', type: ExtraPropertyType::INT, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE, defaultValue: 0),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'dv_float', type: ExtraPropertyType::FLOAT, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE, defaultValue: 1.5),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'dv_string', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE, defaultValue: 'fallback'),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'dv_json', type: ExtraPropertyType::JSON, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE, defaultValue: '{"tier":"bronze"}'),
            new ExtraPropertyDefinition(entityName: 'product', propertyName: 'dv_no_default', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE),
        ];
    }
}
