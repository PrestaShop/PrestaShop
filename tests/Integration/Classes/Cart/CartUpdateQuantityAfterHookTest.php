<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Cart;

use Address;
use Cache;
use Cart;
use Configuration;
use Context;
use Currency;
use Hook;
use Module;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

/**
 * Cart::updateQty() dispatched actionCartUpdateQuantityBefore and nothing afterwards, so a module could
 * see an add-to-cart coming but never that it had happened.
 */
class CartUpdateQuantityAfterHookTest extends KernelTestCase
{
    private const MODULE_NAME = 'ps_cartqtyafterhooktest';

    /**
     * Filled by the fixture module's hook, one entry per dispatch, in order. It lives here rather than on the
     * module class because tests/Resources is not analysed or autoloaded outside PrestaShop's module loader.
     *
     * @var array<int, array<string, mixed>>
     */
    public static array $calls = [];

    private const TABLES_TO_RESTORE = [
        'module',
        'module_shop',
        'hook_module',
        'module_group',
        'authorization_role',
        'module_access',
    ];

    private Cart $cart;
    private Product $product;
    private ?ContextMocker $contextMocker = null;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        // ContainerFinder (used by Cart::getCartRules) reads the global $kernel.
        global $kernel;
        $kernel = self::$kernel;

        // ModuleManager::install() resolves the Language context, which a bare kernel does not provide.
        $this->contextMocker = (new ContextMocker())->mockContext();
        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get(LanguageContextBuilder::class);
        $languageContextBuilder->setLanguageId((int) Configuration::get('PS_LANG_DEFAULT'));

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);
        Module::resetStaticCache();
        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');

        Context::getContext()->currency = Currency::getDefaultCurrency();
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);

        $lang = (int) Configuration::get('PS_LANG_DEFAULT');

        $address = new Address();
        $address->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $address->firstname = 'Hook';
        $address->lastname = 'Tester';
        $address->address1 = '55 rue Raspail';
        $address->alias = microtime() . getmypid();
        $address->city = 'Levallois';
        $address->save();

        $this->product = new Product(null, false, $lang);
        $this->product->id_tax_rules_group = 0;
        $this->product->name = 'Cart hook product';
        $this->product->price = 10;
        $this->product->link_rewrite = 'cart-hook-product';
        $this->product->minimal_quantity = 1;
        $this->product->save();

        $this->cart = new Cart(null, $lang);
        $this->cart->id_currency = Currency::getDefaultCurrencyId();
        $this->cart->id_address_invoice = (int) $address->id;
        $this->cart->save();
        Context::getContext()->cart = $this->cart;

        $this->installModule();
        self::$calls = [];
    }

    protected function tearDown(): void
    {
        self::$calls = [];

        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        if ($moduleManager->isInstalled(self::MODULE_NAME)) {
            $module = Module::getInstanceByName(self::MODULE_NAME);
            if ($module instanceof Module) {
                $module->uninstall();
            }
        }

        DatabaseDump::restoreTables(self::TABLES_TO_RESTORE);

        if (null !== $this->contextMocker) {
            $this->contextMocker->resetContext();
        }

        parent::tearDown();
    }

    public function testAddingAProductDispatchesTheHookAsAnAddition(): void
    {
        $this->assertTrue($this->cart->updateQty(2, (int) $this->product->id));

        $this->assertCount(1, $this->recordedCalls());
        $call = $this->recordedCalls()[0];

        $this->assertSame((int) $this->product->id, $call['id_product']);
        $this->assertSame(2, $call['quantity']);
        $this->assertSame('up', $call['operator']);
        $this->assertTrue($call['product_added_to_cart']);
        // The hook runs after Cart::update(), so the cart already reports the new line.
        $this->assertSame(2, $call['cart_total_quantity']);
    }

    public function testRaisingTheQuantityDispatchesTheHookAsAnUpdate(): void
    {
        $this->cart->updateQty(1, (int) $this->product->id);
        self::$calls = [];

        $this->assertTrue($this->cart->updateQty(3, (int) $this->product->id));

        $this->assertCount(1, $this->recordedCalls());
        $call = $this->recordedCalls()[0];

        $this->assertFalse($call['product_added_to_cart'], 'An existing line was updated, not added.');
        $this->assertSame(4, $call['cart_total_quantity']);
    }

    /**
     * The point of dispatching at the end rather than next to the SQL: a call that bails out must not
     * tell listeners the cart changed.
     */
    public function testARejectedUpdateDispatchesNothing(): void
    {
        $this->product->minimal_quantity = 5;
        $this->product->save();

        // Below the product's minimal quantity, updateQty() returns -1 without touching the cart.
        $this->assertSame(-1, $this->cart->updateQty(1, (int) $this->product->id));
        $this->assertSame([], $this->recordedCalls());
    }

    /**
     * Read through a method: PHPStan narrows the static to `array{}` after a reset and cannot see the hook
     * appending to it.
     *
     * @return array<int, array<string, mixed>>
     */
    private function recordedCalls(): array
    {
        return self::$calls;
    }

    private function installModule(): void
    {
        $moduleManager = ModuleManagerBuilder::getInstance()->build();
        $this->assertTrue((bool) $moduleManager->install(self::MODULE_NAME));

        Cache::clean(Hook::MODULE_LIST_BY_HOOK_KEY . '*');
        Module::resetStaticCache();
    }
}
