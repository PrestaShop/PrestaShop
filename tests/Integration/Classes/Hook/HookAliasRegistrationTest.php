<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Hook;

use Cache;
use Db;
use Hook;
use Module;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * Registering on a hook alias used to resolve the name without aliases, so the alias looked like a
 * hook nobody had declared and a second hook row was created carrying the alias as its name. The
 * module was attached to that row, while everything reading the registration resolves the alias to
 * its canonical hook - which is why TaxManagerFactory never saw a module hooked on `taxManager`.
 */
class HookAliasRegistrationTest extends TestCase
{
    use ContextMockerTrait;

    /** A declared alias of actionTaxManager, see install-dev/data/xml/hook_alias.xml */
    private const ALIAS = 'taxManager';
    private const CANONICAL = 'actionTaxManager';
    private const STUB_MODULE = 'hookaliasregistrationstub';

    private int $moduleId = 0;
    private int $canonicalHookId = 0;
    private HookAliasStubModule $module;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
        Cache::clean('*');

        $this->canonicalHookId = (int) Hook::getIdByName(self::CANONICAL, false, true);
        self::assertGreaterThan(0, $this->canonicalHookId, self::CANONICAL . ' must exist for this test to mean anything');

        Db::getInstance()->insert('module', ['name' => self::STUB_MODULE, 'active' => 1, 'version' => '1.0.0']);
        $this->moduleId = (int) Db::getInstance()->Insert_ID();

        $this->module = new HookAliasStubModule();
        $this->module->id = $this->moduleId;
        $this->module->name = self::STUB_MODULE;
    }

    protected function tearDown(): void
    {
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'hook_module WHERE id_module = ' . $this->moduleId);
        Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'module WHERE id_module = ' . $this->moduleId);
        // Any hook row the registration should no longer be creating, so a failure cannot leak into
        // the next test or into the rest of the suite.
        foreach ([self::ALIAS, 'aHookNameNobodyHasDeclared'] as $name) {
            $ids = Db::getInstance()->executeS(
                'SELECT id_hook FROM ' . _DB_PREFIX_ . "hook WHERE name = '" . pSQL($name) . "'"
            );
            foreach ((array) $ids as $row) {
                $id = (int) $row['id_hook'];
                Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'hook_module WHERE id_hook = ' . $id);
                Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'hook WHERE id_hook = ' . $id);
            }
        }
        Cache::clean('*');

        parent::tearDown();
    }

    /**
     * The assertion that matters is the id the registration wrote, not merely that some lookup
     * later answers. A test that only checked "the module is found" would also pass while the row
     * pointed at a duplicate hook, because which of the two ids a name resolves to then depends on
     * the order the UNION in getAllHookIds() happens to return.
     */
    public function testRegisteringOnAnAliasAttachesTheModuleToTheCanonicalHook(): void
    {
        Hook::registerHook($this->module, self::ALIAS);

        self::assertSame(
            [$this->canonicalHookId],
            $this->registeredHookIds(),
            'the registration must point at the canonical hook, not at a row named after the alias'
        );
    }

    public function testRegisteringOnAnAliasCreatesNoSecondHook(): void
    {
        Hook::registerHook($this->module, self::ALIAS);

        self::assertSame(0, $this->hookRowsNamed(self::ALIAS), 'an alias must not become a hook of its own');
    }

    /**
     * TaxManagerFactory looks the modules up exactly this way.
     */
    public function testTheModuleIsVisibleWhereTheFactoryLooksForIt(): void
    {
        Hook::registerHook($this->module, self::ALIAS);
        Cache::clean('*');

        $modules = Hook::getModulesFromHook(Hook::getIdByName(self::ALIAS, true, true));

        self::assertContains(self::STUB_MODULE, array_column($modules, 'name'));
    }

    /**
     * Registering and unregistering have to resolve the name the same way, or a module can end up
     * holding a row it has no way to remove.
     */
    public function testAnAliasRegistrationCanBeUndoneUnderTheSameAliasName(): void
    {
        Hook::registerHook($this->module, self::ALIAS);
        self::assertNotEmpty($this->registeredHookIds(), 'nothing to unregister, the test would be vacuous');

        Hook::unregisterHook($this->module, self::ALIAS);

        self::assertSame([], $this->registeredHookIds());
    }

    /**
     * A name that is neither a hook nor an alias must still be created, which is what lets a module
     * declare a hook of its own.
     */
    public function testAnUndeclaredHookNameIsStillCreated(): void
    {
        Hook::registerHook($this->module, 'aHookNameNobodyHasDeclared');

        self::assertSame(1, $this->hookRowsNamed('aHookNameNobodyHasDeclared'));
    }

    /**
     * @return int[] the distinct hook ids this module is registered on
     */
    private function registeredHookIds(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT DISTINCT id_hook FROM ' . _DB_PREFIX_ . 'hook_module WHERE id_module = ' . $this->moduleId
        );

        return array_map('intval', array_column((array) $rows, 'id_hook'));
    }

    private function hookRowsNamed(string $name): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . "hook WHERE name = '" . pSQL($name) . "'"
        );
    }
}

/**
 * Implements the listener under the aliased name, which is the case the report is about and the one
 * registerHook()'s own guard accepts through isHookCallableOn().
 */
class HookAliasStubModule extends Module
{
    public function __construct()
    {
        $this->name = 'hookaliasregistrationstub';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        parent::__construct();
    }

    public function hookTaxManager(array $params)
    {
        return null;
    }

    public function hookAHookNameNobodyHasDeclared(array $params)
    {
        return null;
    }
}
