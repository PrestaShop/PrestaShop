<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use Module;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Classes\module\HelperModule;

class ModuleDisableOverridesTest extends KernelTestCase
{
    private const MODULE_NAME = 'pscsx3241';
    private const OTHER_SHOP_ID = 999;
    private const OVERRIDE_FILE = _PS_OVERRIDE_DIR_ . 'classes/Cart.php';

    private Module $module;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once _PS_ROOT_DIR_ . '/tests/Integration/Classes/module/ModuleTest.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        // Read by legacy code resolving the container (SymfonyContainer::getInstance)
        global $kernel;
        $kernel = self::$kernel;

        HelperModule::addModule(self::MODULE_NAME);
        $module = Module::getInstanceByName(self::MODULE_NAME);
        $this->assertInstanceOf(Module::class, $module);
        $this->module = $module;
        $this->assertTrue((bool) $this->module->install(), implode(', ', $this->module->getErrors()));
        $this->assertFileExists(self::OVERRIDE_FILE);
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('module_shop', 'id_module = ' . (int) $this->module->id . ' AND id_shop = ' . self::OTHER_SHOP_ID);
        $this->module->uninstall();
        HelperModule::removeModule(self::MODULE_NAME);
        Module::resetStaticCache();

        global $kernel;
        $kernel = null;
        SymfonyContainer::resetStaticCache();
        parent::tearDown();
    }

    public function testDisablingOnOneShopKeepsTheOverridesAndCanBeUndone(): void
    {
        Db::getInstance()->insert('module_shop', ['id_module' => (int) $this->module->id, 'id_shop' => self::OTHER_SHOP_ID]);

        $this->assertTrue($this->module->disable());
        $this->assertFileExists(self::OVERRIDE_FILE);

        $this->assertTrue($this->module->enable(), implode(', ', $this->module->getErrors()));
        $this->assertFileExists(self::OVERRIDE_FILE);
    }

    public function testDisablingOnEveryShopRemovesTheOverrides(): void
    {
        $this->assertTrue($this->module->disable(true));
        $this->assertFileDoesNotExist(self::OVERRIDE_FILE);

        $this->assertTrue($this->module->enable(), implode(', ', $this->module->getErrors()));
        $this->assertFileExists(self::OVERRIDE_FILE);
    }
}
