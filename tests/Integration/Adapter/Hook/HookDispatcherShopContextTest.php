<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Hook;

use Cache;
use Configuration;
use Context;
use Db;
use Employee;
use Hook;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Hook\HookDispatcher;
use PrestaShop\PrestaShop\Adapter\LegacyHookSubscriber;
use Shop;
use Tests\Resources\DatabaseDump;

class HookDispatcherShopContextTest extends TestCase
{
    private const TABLES_TO_RESTORE = ['shop', 'hook', 'hook_module', 'module', 'module_shop'];

    private const HOOK_NAME = 'displayHookDispatcherShopContextTest';

    private int $defaultShopId;

    private int $secondShopId;

    private int $hookId;

    private int $moduleId;

    private ?Employee $previousEmployee;

    private ?Shop $previousShop;

    private ?int $previousShopContext;

    private int $previousShopGroupId;

    protected function setUp(): void
    {
        parent::setUp();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Shop::resetStaticCache();

        $this->defaultShopId = (int) Configuration::get('PS_SHOP_DEFAULT');

        $secondShop = new Shop();
        $secondShop->name = 'Shop 2';
        $secondShop->id_category = 1;
        $secondShop->id_shop_group = 1;
        $secondShop->add();
        $this->secondShopId = (int) $secondShop->id;
        Shop::resetStaticCache();

        $hook = new Hook();
        $hook->name = self::HOOK_NAME;
        $hook->title = self::HOOK_NAME;
        $hook->add();
        $this->hookId = (int) $hook->id;

        // Hooked in both shops, but only enabled in the second one
        Db::getInstance()->insert('module', [
            'name' => 'hookdispatchershopcontexttest',
            'active' => 1,
            'version' => '1.0.0',
        ]);
        $this->moduleId = (int) Db::getInstance()->Insert_ID();
        Db::getInstance()->insert('module_shop', [
            'id_module' => $this->moduleId,
            'id_shop' => $this->secondShopId,
            'enable_device' => 7,
        ]);
        foreach ([$this->defaultShopId, $this->secondShopId] as $shopId) {
            Db::getInstance()->insert('hook_module', [
                'id_module' => $this->moduleId,
                'id_shop' => $shopId,
                'id_hook' => $this->hookId,
                'position' => 1,
            ]);
        }

        // In the back office the context has an employee and the default shop until the selected shop is applied
        $context = Context::getContext();
        $this->previousEmployee = $context->employee;
        $this->previousShop = $context->shop;
        $this->previousShopContext = Shop::getContext();
        $this->previousShopGroupId = (int) Shop::getContextShopGroupID();
        $context->employee = new Employee(1);
        $this->setShopContext($this->defaultShopId);
    }

    protected function tearDown(): void
    {
        $context = Context::getContext();
        $context->employee = $this->previousEmployee;
        $context->shop = $this->previousShop;
        Shop::setContext($this->previousShopContext ?? Shop::CONTEXT_ALL, $this->previousShopGroupId ?: null);
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Shop::resetStaticCache();
        Cache::clean('*');
        parent::tearDown();
    }

    public function testListenersFollowTheShopSelectedAfterTheDispatcherIsBuilt(): void
    {
        $hookDispatcher = new HookDispatcher(null, [new LegacyHookSubscriber()]);

        $this->setShopContext($this->secondShopId);

        $this->assertSame(
            ['call_' . $this->hookId . '_' . $this->moduleId],
            $this->getListenerMethods($hookDispatcher)
        );
    }

    public function testListenersSkipTheModuleInAShopWhereItIsDisabled(): void
    {
        $hookDispatcher = new HookDispatcher(null, [new LegacyHookSubscriber()]);

        $this->assertSame(
            ['call_' . $this->hookId . '_0'],
            $this->getListenerMethods($hookDispatcher)
        );
    }

    private function setShopContext(int $shopId): void
    {
        Context::getContext()->shop = new Shop($shopId);
        Shop::setContext(Shop::CONTEXT_SHOP, $shopId);
        Cache::clean('*');
    }

    /**
     * @return string[]
     */
    private function getListenerMethods(HookDispatcher $hookDispatcher): array
    {
        return array_map(
            static fn (array $listener): string => $listener[1],
            $hookDispatcher->getListeners(strtolower(self::HOOK_NAME))
        );
    }
}
