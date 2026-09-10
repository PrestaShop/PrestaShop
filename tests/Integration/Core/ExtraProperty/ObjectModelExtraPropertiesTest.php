<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\ExtraProperty;

use Db;
use Language;
use Manufacturer;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShopException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\LanguageTrait;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\ExtraPropertyResetter;
use Tests\Resources\Resetter\LanguageResetter;

/**
 * The native ObjectModel integration of extra properties, end to end on a real entity
 * (manufacturer — cheap to create, and its manufacturer_lang table makes the LANG scope
 * testable): the $entity->extra_properties bag persists on add()/update(), reads back cast
 * to the declared types, follows the loaded language for LANG values, is purged on
 * delete(), and enforces the SAME implicit type validation as every other write path
 * (registry defaults, Admin API, BO form constraint) BEFORE any row is written.
 */
class ObjectModelExtraPropertiesTest extends KernelTestCase
{
    use LanguageTrait;

    private const MODULE = 'extrapropomtest';
    private const DEFAULT_LANG_ID = 1;

    private static ExtraPropertyRegistryInterface $registry;
    private static int $frenchLangId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        // A second language so LANG values are really multi-valued, not a one-row array.
        self::$frenchLangId = self::addLanguageByLocale('fr-FR');
        // The legacy Language static cache predates the insert: without a reset the
        // ObjectModel constructor would treat the new id as unknown and silently fall
        // back to the default language.
        Language::resetStaticCache();

        self::$registry = self::getContainer()->get(ExtraPropertyRegistryInterface::class);
        foreach (self::definitions() as $definition) {
            self::$registry->register($definition);
        }
    }

    public static function tearDownAfterClass(): void
    {
        ExtraPropertyResetter::resetExtraProperties();
        DatabaseDump::restoreTables(['manufacturer', 'manufacturer_lang', 'manufacturer_shop']);
        LanguageResetter::resetLanguages();

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        self::$registry = self::getContainer()->get(ExtraPropertyRegistryInterface::class);
    }

    /**
     * The add() path: values set on a NEW instance BEFORE the first save are persisted by
     * the insert itself — COMMON and LANG (both languages) alike.
     */
    public function testValuesSetBeforeAddPersistOnInsert(): void
    {
        $manufacturer = new Manufacturer();
        $manufacturer->name = 'Extra properties manufacturer ' . uniqid();
        $manufacturer->active = true;
        $manufacturer->extra_properties[self::MODULE]['om_note'] = 'set before add';
        $manufacturer->extra_properties[self::MODULE]['om_count'] = '7';
        $manufacturer->extra_properties[self::MODULE]['om_label'] = [
            self::DEFAULT_LANG_ID => 'english label',
            self::$frenchLangId => 'étiquette française',
        ];

        $this->assertTrue((bool) $manufacturer->add());

        $reloaded = new Manufacturer((int) $manufacturer->id);
        $this->assertSame('set before add', $reloaded->extra_properties[self::MODULE]['om_note']);
        $this->assertSame(7, $reloaded->extra_properties[self::MODULE]['om_count']);
        $this->assertSame(
            [self::DEFAULT_LANG_ID => 'english label', self::$frenchLangId => 'étiquette française'],
            $reloaded->extra_properties[self::MODULE]['om_label']
        );
    }

    public function testCommonValuesPersistOnUpdateAndReadBackTyped(): void
    {
        $manufacturer = $this->createManufacturer();
        $manufacturer->extra_properties[self::MODULE]['om_note'] = 'first batch';
        $manufacturer->extra_properties[self::MODULE]['om_count'] = '42';
        $this->assertTrue((bool) $manufacturer->update());

        // A fresh instance reads through the reader: values come back cast to their
        // declared types (INT string spelling → int), not as raw column strings.
        $reloaded = new Manufacturer((int) $manufacturer->id);
        $this->assertSame('first batch', $reloaded->extra_properties[self::MODULE]['om_note']);
        $this->assertSame(42, $reloaded->extra_properties[self::MODULE]['om_count']);
    }

    public function testLangValueShapeFollowsTheLoadedLanguage(): void
    {
        $manufacturer = $this->createManufacturer();
        $manufacturer->extra_properties[self::MODULE]['om_label'] = [
            self::DEFAULT_LANG_ID => 'localized label',
            self::$frenchLangId => 'libellé localisé',
        ];
        $this->assertTrue((bool) $manufacturer->update());

        // Loaded WITHOUT a language: the LANG value is the full [id_lang => value] array.
        $allLanguages = new Manufacturer((int) $manufacturer->id);
        $this->assertSame(
            [self::DEFAULT_LANG_ID => 'localized label', self::$frenchLangId => 'libellé localisé'],
            $allLanguages->extra_properties[self::MODULE]['om_label']
        );

        // Loaded WITH a language: the LANG value is THAT language's scalar — the same
        // convention as native multilang ObjectModel fields, per language.
        $english = new Manufacturer((int) $manufacturer->id, self::DEFAULT_LANG_ID);
        $this->assertSame('localized label', $english->extra_properties[self::MODULE]['om_label']);
        $french = new Manufacturer((int) $manufacturer->id, self::$frenchLangId);
        $this->assertSame('libellé localisé', $french->extra_properties[self::MODULE]['om_label']);
    }

    /**
     * ObjectModel::add()/update() call validateExtraProperties() BEFORE any row write:
     * a type-incompatible value aborts the whole save — entity row included, so nothing
     * is half-persisted.
     */
    public function testTypeIncompatibleValueAbortsTheWholeSave(): void
    {
        $manufacturer = $this->createManufacturer();
        $originalName = $manufacturer->name;

        $manufacturer->name = 'Renamed with the failing save';
        $manufacturer->extra_properties[self::MODULE]['om_count'] = 'abc';

        try {
            $manufacturer->update();
            $this->fail('A type-incompatible extra property value should abort the ObjectModel save.');
        } catch (PrestaShopException $exception) {
            $this->assertStringContainsString('"int" field type', $exception->getMessage());
        }

        // Nothing was written — not even the entity's own column.
        $reloaded = new Manufacturer((int) $manufacturer->id);
        $this->assertSame($originalName, $reloaded->name);
        $this->assertNull($reloaded->extra_properties[self::MODULE]['om_count']);
    }

    public function testDeletePurgesTheExtraRows(): void
    {
        $manufacturer = $this->createManufacturer();
        $manufacturer->extra_properties[self::MODULE]['om_note'] = 'to be purged';
        $manufacturer->extra_properties[self::MODULE]['om_label'] = [self::DEFAULT_LANG_ID => 'purged too'];
        $manufacturer->update();
        $manufacturerId = (int) $manufacturer->id;

        $this->assertTrue((bool) $manufacturer->delete());

        foreach (['manufacturer_extra', 'manufacturer_extra_lang'] as $table) {
            $this->assertSame(
                0,
                (int) Db::getInstance()->getValue(sprintf(
                    'SELECT COUNT(*) FROM `%s%s` WHERE `id_manufacturer` = %d',
                    _DB_PREFIX_,
                    $table,
                    $manufacturerId
                )),
                $table
            );
        }
    }

    /**
     * @return ExtraPropertyDefinition[]
     */
    private static function definitions(): array
    {
        return [
            new ExtraPropertyDefinition(entityName: 'manufacturer', propertyName: 'om_note', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'manufacturer', propertyName: 'om_count', type: ExtraPropertyType::INT, scope: ExtraPropertyScope::COMMON, moduleName: self::MODULE),
            new ExtraPropertyDefinition(entityName: 'manufacturer', propertyName: 'om_label', type: ExtraPropertyType::STRING, scope: ExtraPropertyScope::LANG, moduleName: self::MODULE),
        ];
    }

    private function createManufacturer(): Manufacturer
    {
        $manufacturer = new Manufacturer();
        $manufacturer->name = 'Extra properties manufacturer ' . uniqid();
        $manufacturer->active = true;
        $this->assertTrue((bool) $manufacturer->add());

        return $manufacturer;
    }
}
