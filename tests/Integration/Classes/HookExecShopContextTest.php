<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Configuration as LegacyConfiguration;
use Context;
use Hook;
use Module;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use RuntimeException;
use Shop as LegacyShop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class HookExecShopContextTest extends KernelTestCase
{
    private const TABLES_TO_RESTORE = ['configuration', 'hook', 'hook_module', 'shop', 'shop_url'];
    private const HOOK_NAME = 'actionHookExecShopContextTest';

    private static int $secondShopId;

    private LegacyShop $initialContextShop;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyConfiguration::resetStaticCache();
        LegacyShop::resetStaticCache();
        self::bootKernel();

        $container = self::$kernel->getContainer();
        $container->get('prestashop.adapter.legacy.configuration')->set('PS_MULTISHOP_FEATURE_ACTIVE', 1);

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $shop = new Shop();
        $shop->setActive(true);
        $shop->setIdCategory(2);
        $shop->setName('test_shop_hook_context');
        $shop->setShopGroup($entityManager->find(ShopGroup::class, 1));
        $shop->setColor('red');
        $shop->setThemeName(Theme::getDefaultTheme());
        $shop->setDeleted(false);
        $entityManager->persist($shop);
        $entityManager->flush();
        self::$secondShopId = (int) $shop->getId();

        LegacyShop::resetStaticCache();

        // Hook::exec() returns before switching the shop when no module is registered on the hook.
        Module::getInstanceByName('ps_banner')->registerHook(self::HOOK_NAME, [1]);
        Cache::clean('*');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        LegacyShop::resetStaticCache();
        LegacyConfiguration::resetStaticCache();
        Cache::clean('*');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->initialContextShop = Context::getContext()->shop;
        Context::getContext()->shop = new LegacyShop(1);
    }

    protected function tearDown(): void
    {
        Context::getContext()->shop = $this->initialContextShop;
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);
        parent::tearDown();
    }

    /**
     * @see https://github.com/PrestaShop/PrestaShop/issues/43219
     */
    public function testExecOnAnotherShopRestoresShopGroupContext(): void
    {
        LegacyShop::setContext(LegacyShop::CONTEXT_GROUP, 1);

        Hook::exec(self::HOOK_NAME, [], null, false, true, false, self::$secondShopId);

        self::assertSame(LegacyShop::CONTEXT_GROUP, LegacyShop::getContext());
        self::assertSame(1, LegacyShop::getContextShopGroupID());
        self::assertNull(LegacyShop::getContextShopID());
        self::assertSame(1, (int) Context::getContext()->shop->id);
    }

    public function testExecOnAnotherShopRestoresShopGroupContextWhenExecutionThrows(): void
    {
        LegacyShop::setContext(LegacyShop::CONTEXT_GROUP, 1);

        $context = Context::getContext();
        $originalController = $context->controller;

        // Simulate an exception while Hook::exec() processes a registered module.
        $context->controller = new class() {
            public function __isset(string $name): bool
            {
                throw new RuntimeException('Simulated hook execution failure');
            }
        };

        try {
            try {
                Hook::exec(self::HOOK_NAME, [], null, false, true, false, self::$secondShopId);
                self::fail('Hook::exec() should have propagated the exception.');
            } catch (RuntimeException $exception) {
                self::assertSame('Simulated hook execution failure', $exception->getMessage());
            }

            self::assertSame(LegacyShop::CONTEXT_GROUP, LegacyShop::getContext());
            self::assertSame(1, LegacyShop::getContextShopGroupID());
            self::assertNull(LegacyShop::getContextShopID());
            self::assertSame(1, (int) $context->shop->id);
        } finally {
            $context->controller = $originalController;
        }
    }

    public function testExecOnAnotherShopRestoresShopContext(): void
    {
        LegacyShop::setContext(LegacyShop::CONTEXT_SHOP, 1);

        Hook::exec(self::HOOK_NAME, [], null, false, true, false, self::$secondShopId);

        self::assertSame(LegacyShop::CONTEXT_SHOP, LegacyShop::getContext());
        self::assertSame(1, LegacyShop::getContextShopID());
        self::assertSame(1, (int) Context::getContext()->shop->id);
    }

    public function testExecOnAnotherShopRestoresAllShopsContext(): void
    {
        LegacyShop::setContext(LegacyShop::CONTEXT_ALL);

        Hook::exec(self::HOOK_NAME, [], null, false, true, false, self::$secondShopId);

        self::assertSame(LegacyShop::CONTEXT_ALL, LegacyShop::getContext());
        self::assertNull(LegacyShop::getContextShopGroupID());
        self::assertNull(LegacyShop::getContextShopID());
        self::assertSame(1, (int) Context::getContext()->shop->id);
    }
}
