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

namespace Tests\Integration\Adapter;

use Configuration;
use Context;
use Exception;
use Hook;
use Module;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ObjectModelComparator;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShop\PrestaShop\Core\Module\ModuleRepository;
use PrestaShopDatabaseException;
use PrestaShopException;
use Product;
use Ps_objectmodel_comparator_test;
use Tests\Integration\Utility\ContextMockerTrait;
use Tools;
use Validate;

class ObjectModelComparatorTest extends TestCase
{
    use ContextMockerTrait;

    /* @var int id current lang id */
    protected int $currentLangId;

    /* @var int current shop id */
    protected int $currentShopId;

    /* @var Product current product object */
    protected Product $productObject;

    /* @const test module name */
    protected const MODULE_NAME = 'ps_objectmodel_comparator_test';

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->currentLangId = (int) Context::getContext()->language->id;
        $this->currentShopId = (int) Context::getContext()->shop->id;
    }

    /**
     * @return ModuleManager
     */
    protected static function getModuleManager(): ModuleManager
    {
        return ModuleManagerBuilder::getInstance()->build();
    }

    /**
     * @return ModuleRepository
     */
    protected static function getModuleRepository(): ModuleRepository
    {
        return ModuleManagerBuilder::getInstance()->buildRepository();
    }

    /**
     * @return Ps_objectmodel_comparator_test|null
     */
    protected static function getModuleToTest(): ?Ps_objectmodel_comparator_test
    {
        $module = self::getModuleRepository()
            ->getModule(self::MODULE_NAME)
            ->getInstance()
        ;

        if (!is_a($module, Ps_objectmodel_comparator_test::class)) {
            return null;
        }

        return $module;
    }

    /**
     * @return void
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    protected function setUp(): void
    {
        parent::setUp();

        self::mockContext();

        // Generate a new product for each test case
        $idProduct = $this->makeProduct();
        $this->productObject = $this->retrieveProductFromDbById($idProduct);

        /*
         * Reinit module hook last call status
         * will be updated in the module every time the hook is called
         * @see Ps_objectmodel_comparator_test::hookActionObjectUpdateBefore and
         * @see Ps_objectmodel_comparator_test::generateHookCallStatusKey
         */
        Configuration::updateValue(
            Ps_objectmodel_comparator_test::PS_OBJECTMODEL_COMPARATOR_LAST_CALL_STATUS,
            'Not_called_during_update_product_' . $idProduct
        );

        Configuration::updateValue(
            Ps_objectmodel_comparator_test::PS_OBJECTMODEL_COMPARATOR_STATUS,
            'Not_called'
        );
    }

    /**
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::backupContext();

        $moduleToTestPath = _PS_TESTS_DIR_ . 'Resources/modules_tests/' . self::MODULE_NAME;

        if (is_dir($moduleToTestPath)) {
            Tools::recurseCopy($moduleToTestPath, _PS_MODULE_DIR_ . self::MODULE_NAME);

            Module::resetStaticCache();

            $module = self::getModuleToTest();

            if (!self::getModuleManager()->isInstalled($module->name)) {
                self::getModuleManager()->install($module->name);
            }
        }
    }

    /**
     * @throws Exception
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        if (self::getModuleManager()->isInstalled(self::MODULE_NAME)) {
            self::getModuleManager()->uninstall(self::MODULE_NAME, true);
        }

        static::resetContext();
    }

    /**
     * Test if a product is successfully created.
     *
     * @return void
     */
    public function testProductSuccessfullyCreated(): void
    {
        $this->assertTrue(Validate::isLoadedObject($this->productObject));
    }

    /**
     * Test if the module has been successfully installed.
     *
     * @return void
     */
    public function testModuleHasBeenInstalled(): void
    {
        $this->assertTrue(self::getModuleManager()->isInstalled(self::MODULE_NAME));
    }

    /**
     * Test if the module has been successfully registered on the "actionObjectProductUpdateBefore" hook.
     *
     * @return void
     */
    public function testModuleHasBeenRegisteredOnTheHook(): void
    {
        $module = self::getModuleToTest();
        $idShop = $this->currentShopId;
        $isModuleRegisteredOnHook = Hook::isModuleRegisteredOnHook($module, 'actionObjectProductUpdateBefore', $idShop);

        $this->assertTrue($isModuleRegisteredOnHook);
    }

    /**
     * Test dynamic hook calls when the ObjectModel has not changed.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws PrestaShopException
     */
    public function testDynamicHookCallIfUnchangedObjectModel(): void
    {
        $oldProductObject = $this->productObject;
        $newProductObject = clone $oldProductObject;
        $newProductObject->price = 189;
        $newProductObject->active = false;

        $comparator = new ObjectModelComparator($oldProductObject, $newProductObject);
        Hook::exec('actionObjectProductUpdateBefore', ['object' => $newProductObject, 'objectComparator' => $comparator]);

        $module = self::getModuleToTest();

        $this->assertEquals(
            'Called_during_update_Product_object_model_' . $oldProductObject->id,
            $module->getHookLastCallStatus()
        );

        $this->assertEquals(
            'Product_' . $oldProductObject->id . '_price_has_been_changed_old_value_100_new_value_189',
            $module->getLastUpdatedProductStatus()
        );
    }

    /**
     * Test dynamic hook calls when the ObjectModel has changed.
     *
     * @return void
     *
     * @throws InvalidArgumentException
     * @throws PrestaShopException
     */
    public function testDynamicHookCallIfChangedObjectModel(): void
    {
        $oldProductObject = $this->productObject;
        $newProductObject = clone $oldProductObject;
        $newProductObject->price = 100;

        $comparator = new ObjectModelComparator($oldProductObject, $newProductObject);
        Hook::exec('actionObjectProductUpdateBefore', ['object' => $newProductObject, 'objectComparator' => $comparator]);

        $module = self::getModuleToTest();

        $this->assertEquals(
            'Called_during_update_Product_object_model_' . $oldProductObject->id,
            $module->getHookLastCallStatus()
        );

        $this->assertEquals(
            'Product_' . $oldProductObject->id . '_price_no_change_detected_old_value_100_new_value_100',
            $module->getLastUpdatedProductStatus()
        );
    }

    /**
     * Tests that the ObjectModel update process is not triggered
     * when no changes are made and the "skipUpdateIfUnchanged" flag is enabled.
     *
     * @return void
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws InvalidArgumentException
     */
    public function testObjectModelUpdateProcessIfUnchangedIAndSkipUpdateIfUnchangedIsEnabled(): void
    {
        $productObject = $this->productObject;
        $productObject->price = 100;
        $productObject->name = 'my_product_name';
        $productObject->skipUpdateIfUnchanged = true;

        $module = self::getModuleToTest();

        $this->assertTrue($productObject->update());
        $this->assertEquals('Not_called', $module->getLastUpdatedProductStatus());
        $this->assertEquals(
            'Not_called_during_update_product_' . $productObject->id,
            $module->getHookLastCallStatus()
        );
    }

    /**
     * Tests that the ObjectModel update process is triggered
     * when no changes are made but the "skipUpdateIfUnchanged" flag is disable.
     *
     * @return void
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws InvalidArgumentException
     */
    public function testObjectModelUpdateProcessIfUnchangedIButSkipUpdateIfUnchangedIsDisabled(): void
    {
        $productObject = $this->productObject;
        $productObject->price = 100;
        $productObject->name = 'my_product_name';
        $productObject->skipUpdateIfUnchanged = false;

        $module = self::getModuleToTest();

        $this->assertTrue($productObject->update());
        $this->assertEquals(
            'Called_during_update_Product_object_model_' . $productObject->id,
            $module->getHookLastCallStatus()
        );

        $this->assertEquals(
            'Product_' . $productObject->id . '_price_no_change_detected_old_value_100_new_value_100',
            $module->getLastUpdatedProductStatus()
        );
    }

    /**
     * Tests that the ObjectModel update process is triggered
     * when changes are made and the "skipUpdateIfUnchanged" flag is enabled.
     *
     * @return void
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws InvalidArgumentException
     */
    public function testObjectModelUpdateProcessIfChangedIAndSkipUpdateIfUnchangedIsEnabled(): void
    {
        $productObject = $this->productObject;
        $productObject->price = 123;
        $productObject->name = 'my_new_product_name';
        $productObject->skipUpdateIfUnchanged = true;

        $module = self::getModuleToTest();

        $this->assertTrue($productObject->update());
        $this->assertEquals(
            'Called_during_update_Product_object_model_' . $productObject->id,
            $module->getHookLastCallStatus()
        );

        $this->assertEquals(
            'Product_' . $productObject->id . '_price_has_been_changed_old_value_100_new_value_123',
            $module->getLastUpdatedProductStatus()
        );
    }

    /**
     * Tests that the ObjectModel update process is triggered
     * when changes are made and the "skipUpdateIfUnchanged" flag is disabled.
     *
     * @return void
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws InvalidArgumentException
     */
    public function testObjectModelUpdateProcessIfChangedIAndSkipUpdateIfUnchangedIsDisabled(): void
    {
        $productObject = $this->productObject;
        $productObject->price = 123;
        $productObject->name = 'my_new_product_name';
        $productObject->skipUpdateIfUnchanged = false;

        $module = self::getModuleToTest();

        $this->assertTrue($productObject->update());
        $this->assertEquals(
            'Called_during_update_Product_object_model_' . $productObject->id,
            $module->getHookLastCallStatus()
        );

        $this->assertEquals(
            'Product_' . $productObject->id . '_price_has_been_changed_old_value_100_new_value_123',
            $module->getLastUpdatedProductStatus()
        );
    }

    /**
     *  Generate a product
     *
     * @return int
     *
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     */
    private function makeProduct(): int
    {
        $productObject = new Product(null, false, $this->currentLangId, $this->currentShopId);
        $productObject->name = 'my_product_name';
        $productObject->price = 100;
        $productObject->active = true;
        $productObject->link_rewrite = Tools::str2url('my_product_name');
        $this->assertTrue((bool) $productObject->add());

        return (int) $productObject->id;
    }

    /**
     * Return product Object model
     *
     * @param int $idProduct
     *
     * @return Product
     */
    private function retrieveProductFromDbById(int $idProduct): Product
    {
        return new Product($idProduct, false, $this->currentLangId, $this->currentShopId);
    }
}
