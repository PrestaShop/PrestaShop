<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Cart;

use Cart;
use Configuration;
use Context;
use Currency;
use Db;
use Language;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\Cart\CartPresenter;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyRegistryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShop\PrestaShop\Core\ExtraProperty\Exception\ExtraPropertyRegistryException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * Extra properties on the CART entity (#41424), end to end:
 *
 *  - registerExtraProperty('cart', ...) creates ps_cart_extra next to ps_cart;
 *  - values persist through the native ObjectModel path ($cart->extra_properties + save());
 *  - the presented cart (CartLazyArray, the $cart Smarty global of every FO page) exposes
 *    them under the snake_case `extra_properties` index;
 *  - LANG and SHOP scopes are cleanly rejected — the cart is a COMMON-only entity (its
 *    id_lang / id_shop are plain columns, there is no cart_lang / cart_shop base table).
 *
 * Plus the reference pattern on ProductLazyArray-equivalent access through the ObjectModel
 * bag, which was previously untested.
 */
class CartExtraPropertiesTest extends KernelTestCase
{
    private const MODULE = 'extrapropcarttest';

    private static ExtraPropertyRegistryInterface $registry;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        self::$registry = self::getContainer()->get(ExtraPropertyRegistryInterface::class);
        self::$registry->register(self::cartDefinition());
    }

    public static function tearDownAfterClass(): void
    {
        self::$registry->unregister(self::cartDefinition(), true);
        DatabaseDump::restoreTables(['cart', 'extra_property_definition']);
        DatabaseDump::removeExtraTables();

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        self::$registry = self::getContainer()->get(ExtraPropertyRegistryInterface::class);

        // The CartLazyArray constructor computes price precision from the context currency.
        $context = Context::getContext();
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
    }

    public function testCartValuePersistsThroughObjectModelSaveAndReload(): void
    {
        $cart = $this->createCart();

        $cart->extra_properties[self::MODULE]['delivery_note'] = 'leave at the pickup point';
        $this->assertTrue((bool) $cart->update());

        // The value row landed in the mirrored storage table.
        $stored = Db::getInstance()->getValue(sprintf(
            'SELECT `%s_delivery_note` FROM `%scart_extra` WHERE `id_cart` = %d',
            self::MODULE,
            _DB_PREFIX_,
            (int) $cart->id
        ));
        $this->assertSame('leave at the pickup point', $stored);

        // A fresh ObjectModel reads it back through its own lazy bag.
        $reloaded = new Cart((int) $cart->id);
        $this->assertSame('leave at the pickup point', $reloaded->extra_properties[self::MODULE]['delivery_note']);
    }

    public function testPresentedCartExposesExtraPropertiesUnderSnakeCaseIndex(): void
    {
        $cart = $this->createCart();
        $cart->extra_properties[self::MODULE]['delivery_note'] = 'ring twice';
        $cart->update();

        // The CartLazyArray is what every FO page receives as the $cart Smarty global.
        $presentedCart = new CartLazyArray(new Cart((int) $cart->id), new CartPresenter());

        $this->assertTrue(isset($presentedCart['extra_properties']));
        $this->assertSame('ring twice', $presentedCart['extra_properties'][self::MODULE]['delivery_note']);
        // The camelCase spelling belongs to the Admin API JSON only — on Smarty surfaces
        // it resolves to nothing (lazy arrays return [] for unknown indexes).
        $this->assertFalse(isset($presentedCart['extraProperties']));
    }

    public function testLangAndShopScopesAreRejectedOnCart(): void
    {
        foreach ([[ExtraPropertyScope::LANG, 'cart_lang'], [ExtraPropertyScope::SHOP, 'cart_shop']] as [$scope, $missingTable]) {
            try {
                self::$registry->register(new ExtraPropertyDefinition(
                    entityName: 'cart',
                    propertyName: 'rejected_scope_note',
                    type: ExtraPropertyType::STRING,
                    scope: $scope,
                    moduleName: self::MODULE,
                ));
                $this->fail(sprintf('%s scope on cart should have been rejected.', $scope->value));
            } catch (ExtraPropertyRegistryException $exception) {
                $this->assertSame(ExtraPropertyRegistryException::BASE_TABLE_NOT_FOUND, $exception->getCode());
                $this->assertStringContainsString($missingTable, $exception->getMessage());
            }
        }
    }

    private function createCart(): Cart
    {
        $cart = new Cart();
        $cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_shop = (int) Context::getContext()->shop->id ?: 1;
        $cart->id_shop_group = (int) Context::getContext()->shop->id_shop_group ?: 1;
        $cart->secure_key = md5('extra-property-cart-test');
        $this->assertTrue((bool) $cart->add());

        return $cart;
    }

    private static function cartDefinition(): ExtraPropertyDefinition
    {
        return new ExtraPropertyDefinition(
            entityName: 'cart',
            propertyName: 'delivery_note',
            type: ExtraPropertyType::STRING,
            scope: ExtraPropertyScope::COMMON,
            moduleName: self::MODULE,
            displayFront: true,
        );
    }
}
