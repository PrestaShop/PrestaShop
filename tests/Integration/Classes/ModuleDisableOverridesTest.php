<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use Module;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Overrides are files: one copy serves the whole installation. Disabling a module on one shop of a
 * multistore used to delete them anyway, which removed the behaviour from every other shop still
 * running that module.
 */
class ModuleDisableOverridesTest extends TestCase
{
    private int $moduleId;

    /**
     * @var array<int, array{id_shop: string}>
     */
    private array $originalAssociations = [];

    private string $originalActive = '1';

    private const OTHER_SHOP_ID = 999;

    protected function setUp(): void
    {
        parent::setUp();

        $this->moduleId = (int) Db::getInstance()->getValue('SELECT id_module FROM ' . _DB_PREFIX_ . 'module');

        if (!$this->moduleId) {
            $this->markTestSkipped('No module installed to exercise disable() with.');
        }

        $this->originalAssociations = Db::getInstance()->executeS(
            'SELECT * FROM ' . _DB_PREFIX_ . 'module_shop WHERE id_module = ' . $this->moduleId
        ) ?: [];
        $this->originalActive = (string) Db::getInstance()->getValue(
            'SELECT active FROM ' . _DB_PREFIX_ . 'module WHERE id_module = ' . $this->moduleId
        );
    }

    protected function tearDown(): void
    {
        // disable() deletes rows, so the module is put back exactly as it was found.
        Db::getInstance()->delete('module_shop', 'id_module = ' . $this->moduleId);
        foreach ($this->originalAssociations as $association) {
            Db::getInstance()->insert('module_shop', $association, false, true, Db::INSERT_IGNORE);
        }
        Db::getInstance()->update('module', ['active' => (int) $this->originalActive], 'id_module = ' . $this->moduleId);

        parent::tearDown();
    }

    private function moduleWithOverrides(): Module&MockObject
    {
        $module = $this->getMockBuilder(Module::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getOverrides', 'uninstallOverrides'])
            ->getMock();
        $module->id = $this->moduleId;
        $module->method('getOverrides')->willReturn(['SomeOverriddenClass']);

        return $module;
    }

    public function testItKeepsOverridesWhileAnotherShopStillUsesTheModule(): void
    {
        Db::getInstance()->insert(
            'module_shop',
            ['id_module' => $this->moduleId, 'id_shop' => self::OTHER_SHOP_ID],
            false,
            true,
            Db::INSERT_IGNORE
        );

        $module = $this->moduleWithOverrides();
        $module->expects($this->never())->method('uninstallOverrides');

        $module->disable();

        $this->assertTrue(
            $module->hasShopAssociations(),
            'The association of the other shop must survive a contextual disable'
        );
    }

    public function testItRemovesOverridesOnceNoShopUsesTheModule(): void
    {
        $module = $this->moduleWithOverrides();
        $module->expects($this->once())->method('uninstallOverrides')->willReturn(true);

        $module->disable(true);

        $this->assertFalse($module->hasShopAssociations());
    }
}
