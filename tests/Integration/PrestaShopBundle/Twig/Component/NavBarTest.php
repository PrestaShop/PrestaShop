<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Twig\Component;

use Cache;
use Context;
use Db;
use Employee;
use PrestaShop\PrestaShop\Core\Module\ModuleManager;
use PrestaShopBundle\Twig\Component\NavBar;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tab;

/**
 * Disabling a module does not touch the tab rows it installed, so the back office menu has to
 * decide on the module state instead. Without that the entries of a disabled module stay in the
 * menu and lead to a controller the module no longer serves.
 */
class NavBarTest extends KernelTestCase
{
    private const MODULE_NAME = 'navbartestmodule';
    private const TAB_CLASS_NAME = 'AdminNavBarTestModule';

    private int $moduleId;

    private int $tabId;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $db = Db::getInstance();

        $db->insert('module', ['name' => self::MODULE_NAME, 'active' => 1, 'version' => '1.0.0']);
        $this->moduleId = (int) $db->Insert_ID();
        $db->insert('module_shop', ['id_module' => $this->moduleId, 'id_shop' => 1]);

        $db->insert('tab', [
            'id_parent' => 0,
            'position' => 1 + (int) $db->getValue('SELECT MAX(position) FROM ' . _DB_PREFIX_ . 'tab WHERE id_parent = 0'),
            'module' => self::MODULE_NAME,
            'class_name' => self::TAB_CLASS_NAME,
            'active' => 1,
            'enabled' => 1,
            // A top level tab without an icon is dropped by processTab(), so the fixture carries one.
            'icon' => 'extension',
        ]);
        $this->tabId = (int) $db->Insert_ID();

        Context::getContext()->employee = new Employee(
            (int) $db->getValue('SELECT id_employee FROM ' . _DB_PREFIX_ . 'employee WHERE id_profile = ' . _PS_ADMIN_PROFILE_)
        );

        $this->forgetCachedState();
    }

    protected function tearDown(): void
    {
        $db = Db::getInstance();
        $db->delete('tab', 'id_tab = ' . $this->tabId);
        $db->delete('module_shop', 'id_module = ' . $this->moduleId);
        $db->delete('module', 'id_module = ' . $this->moduleId);
        $this->forgetCachedState();

        parent::tearDown();
    }

    public function testItKeepsTheTabOfAnEnabledModule(): void
    {
        $this->assertTrue($this->isKeptInTheMenu($this->fixtureTab()));
    }

    public function testItDropsTheTabOfADisabledModule(): void
    {
        // What Module::disable() does: it drops the association of the current shop.
        Db::getInstance()->delete('module_shop', 'id_module = ' . $this->moduleId);
        $this->forgetCachedState();

        $this->assertFalse($this->isKeptInTheMenu($this->fixtureTab()));
    }

    public function testItKeepsATabThatBelongsToNoModule(): void
    {
        $coreTab = $this->fixtureTab();
        $coreTab['module'] = null;

        $this->assertTrue($this->isKeptInTheMenu($coreTab));
    }

    /**
     * @return array<string, mixed>
     */
    private function fixtureTab(): array
    {
        return Tab::getTab((int) Context::getContext()->language->id, $this->tabId);
    }

    /**
     * @param array<string, mixed> $tab
     */
    private function isKeptInTheMenu(array $tab): bool
    {
        $container = self::getContainer();
        $navBar = new NavBar(
            $container->get('prestashop.adapter.legacy.context'),
            $container->get('logger'),
            $container->get('PrestaShopBundle\Twig\Layout\MenuBuilder'),
            _PS_VERSION_,
            $container->get(ModuleManager::class)
        );

        $isValidTab = new ReflectionMethod(NavBar::class, 'isValidTab');
        $isValidTab->setAccessible(true);

        return (bool) $isValidTab->invoke($navBar, $tab);
    }

    private function forgetCachedState(): void
    {
        Tab::resetStaticCache();
        Cache::clean('Module::isEnabled' . self::MODULE_NAME);
    }
}
