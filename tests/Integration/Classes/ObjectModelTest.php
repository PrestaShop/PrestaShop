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
use ObjectModel;
use Language;
use PHPUnit\Framework\TestCase;
use Shop;

class ObjectModelTest extends TestCase
{
    /**
     * @var int
     */
    protected $defaultLanguageId;

    /**
     * @var int
     */
    protected $secondLanguageId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->installTestableObjectTables();;
        $this->installLanguages();
    }

    protected function installTestableObjectTables(): void
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

    protected function installLanguages(): void
    {
        $this->defaultLanguageId = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->secondLanguageId = (int) Language::getIdByIso('fr');
        if ($this->secondLanguageId) {
            return;
        }

        $language = new Language();
        $language->name = 'fr';
        $language->iso_code = 'fr';
        $language->locale = 'fr-FR';
        $language->language_code = 'fr-FR';
        $language->add();
        $this->secondLanguageId = (int) $language->id;
    }

    public function testAdd(): void
    {
        $quantity = 42;
        $localizedNames = [
            $this->defaultLanguageId => 'Default name',
            $this->secondLanguageId => 'Second name',
        ];

        $newObject = new TestableObjectModel();
        $newObject->quantity = $quantity;
        $newObject->enabled = true;
        $newObject->name = $localizedNames;

        $this->assertTrue((bool) $newObject->add());
        $this->assertNotNull($newObject->id);
        // Only the id field is filled, the identified primary key should also be updated
        // $this->assertNotNull($object->id_testable_object);
        $createdId = $newObject->id;

        $multiLangObject = new TestableObjectModel($createdId);
        $this->assertEquals($createdId, $multiLangObject->id);
        $this->assertEquals($createdId, $multiLangObject->id_testable_object);
        $this->assertEquals($quantity, $multiLangObject->quantity);
        $this->assertTrue((bool) $multiLangObject->enabled);
        $this->assertEquals($localizedNames, $multiLangObject->name);

        $defaultLangObject = new TestableObjectModel($createdId, $this->defaultLanguageId);
        $this->assertEquals($localizedNames[$this->defaultLanguageId], $defaultLangObject->name);

        $secondLangObject = new TestableObjectModel($createdId, $this->secondLanguageId);
        $this->assertEquals($localizedNames[$this->secondLanguageId], $secondLangObject->name);
    }
}

class TestableObjectModel extends ObjectModel {
    /**
     * @var int
     */
    public $id_testable_object;

    /**
     * This field is multilang and multi shop
     *
     * @var string|string[]
     */
    public $name;

    /**
     * This field is global to all shops
     *
     * @var int
     */
    public $quantity;

    /**
     * This field is multishop
     *
     * @var bool
     */
    public $enabled;

    public static $definition = [
        'table' => 'testable_object',
        'primary' => 'id_testable_object',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => [
            // Classic fields
            'quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedFloat'],
            // Multi lang fields
            'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName', 'required' => false, 'size' => 128],
            // Shop fields
            'enabled' => ['type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'],
        ]
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation('testable_object', ['type' => 'shop']);
        parent::__construct($id, $id_lang, $id_shop);
    }
}
