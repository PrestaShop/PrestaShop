<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Db;
use Language;
use PHPUnit\Framework\TestCase;
use Shop;
use Tests\Resources\classes\TestableObjectModel;

class ObjectModelTest extends TestCase
{
    private const DEFAULT_LANGUAGE_PLACEHOLDER = 'default_language';
    private const SECOND_LANGUAGE_PLACEHOLDER = 'second_language';

    private const DEFAULT_SHOP_PLACEHOLDER = 'default_shop';
    private const SECOND_SHOP_PLACEHOLDER = 'second_shop';

    /**
     * @var int
     */
    private $defaultLanguageId;

    /**
     * @var int
     */
    private $secondLanguageId;

    /**
     * @var int
     */
    private $defaultShopId;

    /**
     * @var int
     */
    private $secondShopId;

    protected function setUp(): void
    {
        parent::setUp();

        // Because if process isolation we cannot rely on setupBeforeClass because it is called on each run, so we have
        // to initialize our test data during setup which implies checking at each time if the fixtures data have been
        // created or not
        $this->installTestableObjectTables();
        $this->installLanguages();
        $this->installShops();
    }

    public function testAdd(): void
    {
        $quantity = 42;
        $localizedNames = [
            $this->defaultLanguageId => 'Default name',
            $this->secondLanguageId => 'Second name',
        ];

        // First add the object with specified data (mix of common, multishop, multilang fields)
        $newObject = new TestableObjectModel();
        $newObject->quantity = $quantity;
        $newObject->enabled = true;
        $newObject->name = $localizedNames;

        // Then check that the object is correctly added and its ID was generated
        $this->assertTrue((bool) $newObject->add());
        $this->assertNotNull($newObject->id);
        // Only the id field is filled, the identified primary key (id_testable_object) should also be updated,
        // but the add method doesn't update it (it would be a nice, and simple, improvement)
        $createdId = (int) $newObject->id;

        // Then get the object from DB without specifying the lang ID so that multilang values are returned, check that
        // the saved data matches the initial inputs
        $multiLangObject = new TestableObjectModel($createdId);
        $this->assertEquals($createdId, $multiLangObject->id);
        $this->assertEquals($createdId, $multiLangObject->id_testable_object);
        $this->assertEquals($quantity, $multiLangObject->quantity);
        $this->assertTrue((bool) $multiLangObject->enabled);
        $this->assertEquals($localizedNames, $multiLangObject->name);

        // Finally, fetch the object with a specified langId the multilang field is now a simple string
        $defaultLangObject = new TestableObjectModel($createdId, $this->defaultLanguageId);
        $this->assertEquals($localizedNames[$this->defaultLanguageId], $defaultLangObject->name);

        $secondLangObject = new TestableObjectModel($createdId, $this->secondLanguageId);
        $this->assertEquals($localizedNames[$this->secondLanguageId], $secondLangObject->name);
    }

    public function testUpdate(): void
    {
        $quantity = 42;
        $localizedNames = [
            $this->defaultLanguageId => 'Default name',
            $this->secondLanguageId => 'Second name',
        ];

        // To check update we must first create a instance to be modified later
        $newObject = new TestableObjectModel();
        $newObject->quantity = $quantity;
        $newObject->enabled = true;
        $newObject->name = $localizedNames;

        $this->assertTrue((bool) $newObject->add());
        $this->assertNotNull($newObject->id);
        $createdId = (int) $newObject->id;

        // Now that the instance is created in DB we update its content and perform an update
        $newLocalizedNames = [
            $this->defaultLanguageId => 'New Default name',
            $this->secondLanguageId => 'New Second name',
        ];
        $newObject->enabled = false;
        $newObject->quantity = 51;
        $newObject->name = $newLocalizedNames;
        $this->assertTrue((bool) $newObject->update());

        // Then we fetch the object (in multilang) and check that the data in DB matches the updated values (not the
        // initial ones)
        $multiLangObject = new TestableObjectModel($createdId);
        $this->assertEquals($createdId, $multiLangObject->id);
        $this->assertEquals($createdId, $multiLangObject->id_testable_object);
        $this->assertEquals(51, $multiLangObject->quantity);
        $this->assertFalse((bool) $multiLangObject->enabled);
        $this->assertEquals($newLocalizedNames, $multiLangObject->name);

        // Then we set back the initial data and update the object again (just to check that multiple object instances
        // do update the same table row appropriately)
        $multiLangObject->quantity = $quantity;
        $multiLangObject->enabled = true;
        $multiLangObject->name = $localizedNames;
        $this->assertTrue((bool) $multiLangObject->update());

        $defaultLangObject = new TestableObjectModel($createdId, $this->defaultLanguageId);
        $this->assertEquals($localizedNames[$this->defaultLanguageId], $defaultLangObject->name);

        $secondLangObject = new TestableObjectModel($createdId, $this->secondLanguageId);
        $this->assertEquals($localizedNames[$this->secondLanguageId], $secondLangObject->name);
    }

    /**
     * @dataProvider getPartialUpdates
     *
     * @param array $initialProperties
     * @param array $updatedProperties
     * @param array $fieldsToUpdate
     * @param array $expectedProperties
     */
    public function testPartialUpdate(array $initialProperties, array $updatedProperties, array $fieldsToUpdate, array $expectedProperties): void
    {
        // First create the initial object in DB
        $newObject = new TestableObjectModel();
        $this->applyModifications($newObject, $initialProperties);
        $this->assertTrue((bool) $newObject->add());
        $this->assertNotNull($newObject->id);
        $createdId = (int) $newObject->id;

        // Then fetch the created object and apply the modifications
        $objectToUpdate = new TestableObjectModel($createdId);
        $this->applyModifications($objectToUpdate, $updatedProperties);
        if (isset($fieldsToUpdate['name'])) {
            $fieldsToUpdate['name'] = $this->convertLocalizedValue($fieldsToUpdate['name']);
        }

        // We set the fields to be updated. They may not match the whole fields, that's the point of partial update
        $objectToUpdate->setFieldsToUpdate($fieldsToUpdate);
        $this->assertTrue((bool) $objectToUpdate->update());

        // Finally, check that the object in DB matches the expected values (only some fields should have been updated)
        $updatedObject = new TestableObjectModel($createdId);
        $this->checkObjectFields($updatedObject, $expectedProperties);
    }

    public function getPartialUpdates(): iterable
    {
        $initQuantity = 42;
        $updatedQuantity = 51;
        $localizedNames = [
            self::DEFAULT_LANGUAGE_PLACEHOLDER => 'Default name',
            self::SECOND_LANGUAGE_PLACEHOLDER => 'Second name',
        ];
        $updatedLocalizedNames = [
            self::DEFAULT_LANGUAGE_PLACEHOLDER => 'Updated Default name',
            self::SECOND_LANGUAGE_PLACEHOLDER => 'Updated Second name',
        ];

        $initialValues = [
            'quantity' => $initQuantity,
            'enabled' => true,
            'name' => $localizedNames,
        ];

        // First test updates all the fields (but explicitly register them for update)
        yield [
            $initialValues,
            [
                'quantity' => $updatedQuantity,
                'enabled' => false,
                'name' => $updatedLocalizedNames,
            ],
            [
                'quantity' => true,
                'enabled' => true,
                'name' => [
                    self::DEFAULT_LANGUAGE_PLACEHOLDER => true,
                    self::SECOND_LANGUAGE_PLACEHOLDER => true,
                ],
            ],
            [
                'quantity' => $updatedQuantity,
                'enabled' => 0,
                'name' => $updatedLocalizedNames,
            ],
        ];

        // Modify multiple fields but only update quantity (classic value)
        yield [
            $initialValues,
            [
                'quantity' => $updatedQuantity,
                'enabled' => false,
                'name' => $updatedLocalizedNames,
            ],
            [
                'quantity' => true,
            ],
            [
                'quantity' => $updatedQuantity,
                'enabled' => 1,
                'name' => $localizedNames,
            ],
        ];

        // Modify multiple fields but only update enabled (multishop value)
        yield [
            $initialValues,
            [
                'quantity' => $updatedQuantity,
                'enabled' => false,
                'name' => $updatedLocalizedNames,
            ],
            [
                'enabled' => true,
            ],
            [
                'quantity' => $initQuantity,
                'enabled' => 0,
                'name' => $localizedNames,
            ],
        ];

        // Modify multiple fields but only update name (multilang value)
        yield [
            $initialValues,
            [
                'quantity' => $updatedQuantity,
                'enabled' => false,
                'name' => $updatedLocalizedNames,
            ],
            [
                'name' => [
                    self::DEFAULT_LANGUAGE_PLACEHOLDER => true,
                    self::SECOND_LANGUAGE_PLACEHOLDER => true,
                ],
            ],
            [
                'quantity' => $initQuantity,
                'enabled' => 1,
                'name' => $updatedLocalizedNames,
            ],
        ];

        // Modify multiple fields but only update one language for name (multilang value)
        yield [
            $initialValues,
            [
                'quantity' => $updatedQuantity,
                'enabled' => false,
                'name' => $updatedLocalizedNames,
            ],
            [
                'name' => [
                    self::SECOND_LANGUAGE_PLACEHOLDER => true,
                ],
            ],
            [
                'quantity' => $initQuantity,
                'enabled' => 1,
                'name' => [
                    self::DEFAULT_LANGUAGE_PLACEHOLDER => $localizedNames[self::DEFAULT_LANGUAGE_PLACEHOLDER],
                    self::SECOND_LANGUAGE_PLACEHOLDER => $updatedLocalizedNames[self::SECOND_LANGUAGE_PLACEHOLDER],
                ],
            ],
        ];
    }

    /**
     * @dataProvider getMultiShopValues
     *
     * @param array $initialProperties
     * @param array $initialShops
     * @param array $multiShopValues
     * @param array $expectedMultiShopValues
     */
    public function testMultiShopUpdate(array $initialProperties, array $initialShops, array $multiShopValues, array $expectedMultiShopValues): void
    {
        // First create the initial object
        $newObject = new TestableObjectModel();

        // Define the shop associated to the entity based on the parameter
        $initialShopIds = [];
        if (in_array(static::DEFAULT_SHOP_PLACEHOLDER, $initialShops)) {
            $initialShopIds[] = $this->defaultShopId;
        }
        if (in_array(static::SECOND_SHOP_PLACEHOLDER, $initialShops)) {
            $initialShopIds[] = $this->secondShopId;
        }
        $newObject->id_shop_list = $initialShopIds;

        // Set the object's fields based on the input parameters and add the object to DB
        $this->applyModifications($newObject, $initialProperties);
        $this->assertTrue((bool) $newObject->add());
        $this->assertNotNull($newObject->id);
        $createdId = (int) $newObject->id;

        // Now we update the values for different shops
        foreach ($multiShopValues as $shopId => $updateValues) {
            $shopId = $shopId === static::DEFAULT_SHOP_PLACEHOLDER ? $this->defaultShopId : $this->secondShopId;
            // Fetch the object with specified shopId, then apply modifications
            $objectToUpdate = new TestableObjectModel($createdId, null, $shopId);
            // We force this field so that only one shop is updated
            $objectToUpdate->id_shop_list = [$shopId];
            $this->applyModifications($objectToUpdate, $updateValues);
            $this->assertTrue((bool) $objectToUpdate->update());
        }

        // Finally, we fetch the object for each shop separately and check that the values match the expected data
        foreach ($expectedMultiShopValues as $shopId => $expectedValues) {
            $shopId = $shopId === static::DEFAULT_SHOP_PLACEHOLDER ? $this->defaultShopId : $this->secondShopId;
            $updatedObject = new TestableObjectModel($createdId, null, $shopId);
            $this->checkObjectFields($updatedObject, $expectedValues);
        }
    }

    public function getMultiShopValues(): iterable
    {
        $initQuantity = 42;
        $localizedNames = [
            self::DEFAULT_LANGUAGE_PLACEHOLDER => 'Default name',
            self::SECOND_LANGUAGE_PLACEHOLDER => 'Second name',
        ];
        $updatedLocalizedNames = [
            self::DEFAULT_LANGUAGE_PLACEHOLDER => 'Updated Default name',
            self::SECOND_LANGUAGE_PLACEHOLDER => 'Updated Second name',
        ];

        $initialValues = [
            'quantity' => $initQuantity,
            'enabled' => true,
            'name' => $localizedNames,
        ];

        // Object is created in both shops, both shops are modified the same way
        yield [
            $initialValues,
            [self::DEFAULT_SHOP_PLACEHOLDER, self::SECOND_SHOP_PLACEHOLDER],
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'enabled' => false,
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'enabled' => false,
                ],
            ],
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'enabled' => 0,
                    'name' => $localizedNames,
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'enabled' => 0,
                    'name' => $localizedNames,
                ],
            ],
        ];

        // Object is create for both shops, one shop has enabled modified the other one its name
        yield [
            $initialValues,
            [self::DEFAULT_SHOP_PLACEHOLDER, self::SECOND_SHOP_PLACEHOLDER],
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'name' => $updatedLocalizedNames,
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'enabled' => false,
                ],
            ],
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'enabled' => 1,
                    'name' => $updatedLocalizedNames,
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'enabled' => 0,
                    'name' => $localizedNames,
                ],
            ],
        ];
    }

    /**
     * @dataProvider getPartialMultiShopValues
     *
     * @param array $initialProperties
     * @param array $initialShops
     * @param array $multiShopValues
     * @param array $multiShopFieldsToUpdate
     * @param array $expectedMultiShopValues
     */
    public function testPartialMultiShopUpdate(
        array $initialProperties,
        array $initialShops,
        array $multiShopValues,
        array $multiShopFieldsToUpdate,
        array $expectedMultiShopValues
    ): void {
        // First create the initial object
        $newObject = new TestableObjectModel();

        // Define the shop associated to the entity based on the parameter
        $initialShopIds = [];
        if (in_array(static::DEFAULT_SHOP_PLACEHOLDER, $initialShops)) {
            $initialShopIds[] = $this->defaultShopId;
        }
        if (in_array(static::SECOND_SHOP_PLACEHOLDER, $initialShops)) {
            $initialShopIds[] = $this->secondShopId;
        }
        $newObject->id_shop_list = $initialShopIds;

        // Set the object's fields based on the input parameters and add the object to DB
        $this->applyModifications($newObject, $initialProperties);
        $this->assertTrue((bool) $newObject->add());
        $this->assertNotNull($newObject->id);
        $createdId = (int) $newObject->id;

        // Now we update the values for different shops
        foreach ($multiShopValues as $shopId => $updateValues) {
            $fieldsToUpdate = $multiShopFieldsToUpdate[$shopId];
            $shopId = $shopId === static::DEFAULT_SHOP_PLACEHOLDER ? $this->defaultShopId : $this->secondShopId;
            // Fetch the object with specified shopId
            $objectToUpdate = new TestableObjectModel($createdId, null, $shopId);
            // We force this field so that only one shop is updated
            $objectToUpdate->id_shop_list = [$shopId];

            // Apply modifications on multiple fields
            $this->applyModifications($objectToUpdate, $updateValues);
            if (isset($fieldsToUpdate['name'])) {
                $fieldsToUpdate['name'] = $this->convertLocalizedValue($fieldsToUpdate['name']);
            }

            // But limit the update to specific fields
            $objectToUpdate->setFieldsToUpdate($fieldsToUpdate);
            $this->assertTrue((bool) $objectToUpdate->update());
        }

        // Finally, we fetch the object for each shop separately and check that the values match the expected data
        foreach ($expectedMultiShopValues as $shopId => $expectedValues) {
            $shopId = $shopId === static::DEFAULT_SHOP_PLACEHOLDER ? $this->defaultShopId : $this->secondShopId;
            $updatedObject = new TestableObjectModel($createdId, null, $shopId);
            $this->checkObjectFields($updatedObject, $expectedValues);
        }
    }

    public function getPartialMultiShopValues(): iterable
    {
        $initQuantity = 42;
        $localizedNames = [
            self::DEFAULT_LANGUAGE_PLACEHOLDER => 'Default name',
            self::SECOND_LANGUAGE_PLACEHOLDER => 'Second name',
        ];
        $updatedLocalizedNames = [
            self::DEFAULT_LANGUAGE_PLACEHOLDER => 'Updated Default name',
            self::SECOND_LANGUAGE_PLACEHOLDER => 'Updated Second name',
        ];

        $initialValues = [
            'quantity' => $initQuantity,
            'enabled' => true,
            'name' => $localizedNames,
        ];

        // Object is added to both shops, update enabled only via partial update on both shops
        yield [
            // Initial values for creation
            $initialValues,
            // List of associated shops
            [self::DEFAULT_SHOP_PLACEHOLDER, self::SECOND_SHOP_PLACEHOLDER],
            // Modifications on the object
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'enabled' => false,
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'enabled' => false,
                ],
            ],
            // Define which field are registered for partial update
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'enabled' => true,
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'enabled' => true,
                ],
            ],
            // Expected values after partial update
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'enabled' => 0,
                    'name' => $localizedNames,
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'enabled' => 0,
                    'name' => $localizedNames,
                ],
            ],
        ];

        // Object is added on both shops, both shop have their name and enabled modified But name is partially updated
        // for default shop only (both languages), while enabled is partially updated for second shop only The other
        // fields should not be modified
        yield [
            $initialValues,
            [self::DEFAULT_SHOP_PLACEHOLDER, self::SECOND_SHOP_PLACEHOLDER],
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'name' => $updatedLocalizedNames,
                    'enabled' => false,
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'name' => $updatedLocalizedNames,
                    'enabled' => false,
                ],
            ],
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'name' => [
                        self::DEFAULT_LANGUAGE_PLACEHOLDER => true,
                        self::SECOND_LANGUAGE_PLACEHOLDER => true,
                    ],
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'enabled' => true,
                ],
            ],
            [
                self::DEFAULT_SHOP_PLACEHOLDER => [
                    'enabled' => 1,
                    'name' => $updatedLocalizedNames,
                ],
                self::SECOND_SHOP_PLACEHOLDER => [
                    'enabled' => 0,
                    'name' => $localizedNames,
                ],
            ],
        ];
    }

    /**
     * @param TestableObjectModel $object
     * @param array $expectedProperties
     */
    private function checkObjectFields(TestableObjectModel $object, array $expectedProperties): void
    {
        foreach ($expectedProperties as $field => $expectedValue) {
            if (is_array($expectedValue)) {
                $expectedValue = $this->convertLocalizedValue($expectedValue);
            }
            $this->assertEquals($expectedValue, $object->{$field});
        }
    }

    /**
     * @param TestableObjectModel $object
     * @param array $updatedProperties
     */
    private function applyModifications(TestableObjectModel $object, array $updatedProperties): void
    {
        foreach ($updatedProperties as $field => $value) {
            $object->{$field} = is_array($value) ? $this->convertLocalizedValue($value) : $value;
        }
    }

    /**
     * @param array $value
     *
     * @return array
     */
    private function convertLocalizedValue(array $value): array
    {
        $localizedValue = [];
        if (isset($value[self::DEFAULT_LANGUAGE_PLACEHOLDER])) {
            $localizedValue[$this->defaultLanguageId] = $value[self::DEFAULT_LANGUAGE_PLACEHOLDER];
        }
        if (isset($value[self::SECOND_LANGUAGE_PLACEHOLDER])) {
            $localizedValue[$this->secondLanguageId] = $value[self::SECOND_LANGUAGE_PLACEHOLDER];
        }

        return $localizedValue;
    }

    /*
     * Following are fixtures installation functions
     */

    private function installTestableObjectTables(): void
    {
        $testableObjectSqlFile = dirname(__DIR__, 2) . '/Resources/sql/install_testable_object.sql';
        $sqlRequest = file_get_contents($testableObjectSqlFile);
        $sqlRequest = preg_replace('/PREFIX_/', _DB_PREFIX_, $sqlRequest);

        $dbCollation = Db::getInstance()->getValue('SELECT @@collation_database');
        $allowedCollations = ['utf8mb4_general_ci', 'utf8mb4_unicode_ci'];
        $collateReplacement = (empty($dbCollation) || !in_array($dbCollation, $allowedCollations)) ? '' : 'COLLATE ' . $dbCollation;
        $sqlRequest = preg_replace('/COLLATION/', $collateReplacement, $sqlRequest);

        $sqlRequest = preg_replace('/ENGINE_TYPE/', _MYSQL_ENGINE_, $sqlRequest);

        $db = Db::getInstance();
        $db->execute($sqlRequest);
    }

    private function installLanguages(): void
    {
        $this->defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->secondLanguageId = (int) Language::getIdByIso('fr');
        if ($this->secondLanguageId) {
            $this->cleanUndesiredLanguages();

            return;
        }

        $language = new Language();
        $language->name = 'fr';
        $language->iso_code = 'fr';
        $language->locale = 'fr-FR';
        $language->language_code = 'fr-FR';
        $language->add();
        $this->secondLanguageId = (int) $language->id;
        $this->cleanUndesiredLanguages();
    }

    private function installShops(): void
    {
        $this->defaultShopId = (int) Configuration::get('PS_SHOP_DEFAULT');
        $this->secondShopId = Shop::getIdByName('Shop 2');
        if ($this->secondShopId) {
            $this->cleanUndesiredShops();

            return;
        }

        $shop = new Shop();
        $shop->name = 'Shop 2';
        $shop->id_category = 1;
        $shop->id_shop_group = 1;
        $shop->domain = Configuration::get('PS_SHOP_DOMAIN');
        $shop->physical_uri = '/';
        $shop->add();
        $this->secondShopId = (int) $shop->id;
        $this->cleanUndesiredShops();
    }

    /**
     * We need to remove extra languages because they would mess with the expected content,
     * but we don't want to use DatabaseDump::restoredDB because all the tests are process
     * isolated and it would be dumped on each test which would take too long.
     */
    private function cleanUndesiredLanguages(): void
    {
        // Clean undesired languages
        $db = Db::getInstance();
        $db->execute(sprintf(
            'DELETE FROM %slang WHERE id_lang != %d AND id_lang != %d',
            _DB_PREFIX_,
            $this->defaultLanguageId,
            $this->secondLanguageId
        ));
        Language::resetCache();
    }

    /**
     * We need to remove extra shops because they would mess with the expected content,
     * but we don't want to use DatabaseDump::restoredDB because all the tests are process
     * isolated and it would be dumped on each test which would take too long.
     */
    private function cleanUndesiredShops(): void
    {
        // Clean undesired shops
        $db = Db::getInstance();
        $db->execute(sprintf(
            'DELETE FROM %sshop WHERE id_shop != %d AND id_shop != %d',
            _DB_PREFIX_,
            $this->defaultShopId,
            $this->secondShopId
        ));
    }
}
