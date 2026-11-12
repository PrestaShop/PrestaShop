<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use Module;
use PHPUnit\Framework\TestCase;
use Tests\Resources\DatabaseDump;

class ModuleTabsEnabledTest extends TestCase
{
    private const MODULE_NAME = 'translationtest';

    protected function tearDown(): void
    {
        DatabaseDump::restoreTables(['module', 'module_shop', 'tab']);
        Module::resetStaticCache();
        parent::tearDown();
    }

    public function testDisablingTheModuleOnEveryShopDisablesItsTabs(): void
    {
        // The fixture module must be installed for enable() to work, which the test database does not guarantee
        if (!Module::getModuleIdByName(self::MODULE_NAME)) {
            Db::getInstance()->insert('module', ['name' => self::MODULE_NAME, 'active' => 1, 'version' => '1.0.0']);
            Db::getInstance()->insert('module_shop', ['id_module' => (int) Db::getInstance()->Insert_ID(), 'id_shop' => 1, 'enable_device' => 7]);
            Module::resetStaticCache();
        }
        $module = Module::getInstanceByName(self::MODULE_NAME);
        $this->assertInstanceOf(Module::class, $module);
        $this->assertGreaterThan(0, (int) $module->id);
        Db::getInstance()->delete('tab', 'module = \'' . self::MODULE_NAME . '\'');
        foreach (['AdminModuleTabsProbeFirst', 'AdminModuleTabsProbeSecond'] as $className) {
            Db::getInstance()->insert('tab', [
                'id_parent' => -1,
                'position' => 0,
                'module' => self::MODULE_NAME,
                'class_name' => $className,
                'active' => 1,
                'enabled' => 1,
            ]);
        }
        $this->assertSame([1, 1], $this->getTabsEnabled(), 'the module tabs should start enabled');

        $this->assertTrue($module->disable(true));
        $this->assertSame([0, 0], $this->getTabsEnabled());

        $this->assertTrue($module->enable());
        $this->assertSame([1, 1], $this->getTabsEnabled());
    }

    /**
     * @return int[]
     */
    private function getTabsEnabled(): array
    {
        return array_map('intval', array_column(
            Db::getInstance()->executeS(
                'SELECT enabled FROM ' . _DB_PREFIX_ . 'tab WHERE module = \'' . self::MODULE_NAME . '\' ORDER BY id_tab'
            ),
            'enabled'
        ));
    }
}
