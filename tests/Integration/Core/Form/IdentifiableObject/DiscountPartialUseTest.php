<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Form\IdentifiableObject;

use CartRule;
use Configuration;
use Context;
use Currency;
use Db;
use Language;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\DiscountFormDataHandler;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\DiscountFormDataProvider;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The previous discount form let a merchant allow partial use of an amount discount, and the cart
 * rule has always stored it. The rewritten form has to carry it in both directions or the setting
 * is invisible and silently reset on the next save.
 */
class DiscountPartialUseTest extends KernelTestCase
{
    private const CART_LEVEL_DISCOUNT_TYPE_ID = 2;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::resetDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetDatabase();
    }

    protected static function resetDatabase(): void
    {
        DatabaseDump::restoreTables(['cart_rule', 'cart_rule_lang', 'cart_rule_shop']);
    }

    /**
     * @dataProvider getStoredAndExpectedFlag
     */
    public function testTheStoredFlagReachesTheForm(int $stored, bool $expected): void
    {
        $discountId = $this->createAmountDiscount($stored);

        $data = $this->getProvider()->getData($discountId);

        self::assertSame($expected, $data['value']['allow_partial_use']);
    }

    public function getStoredAndExpectedFlag(): iterable
    {
        yield 'not allowed' => [0, false];
        yield 'allowed' => [1, true];
    }

    /**
     * @dataProvider getSubmittedAndExpectedFlag
     */
    public function testWhatTheFormSubmitsIsStored(int $stored, bool $submitted, int $expected): void
    {
        $discountId = $this->createAmountDiscount($stored);

        $data = $this->getProvider()->getData($discountId);
        $data['value']['allow_partial_use'] = $submitted;
        $this->getHandler()->update($discountId, $data);

        self::assertSame(
            $expected,
            (int) Db::getInstance()->getValue(
                'SELECT partial_use FROM ' . _DB_PREFIX_ . 'cart_rule WHERE id_cart_rule = ' . $discountId
            )
        );
    }

    public function getSubmittedAndExpectedFlag(): iterable
    {
        yield 'turning it on' => [0, true, 1];
        yield 'turning it off' => [1, false, 0];
    }

    /**
     * A discount made before the option existed on the form allowed partial use, so a new one has to
     * start the same way rather than silently taking it away.
     */
    public function testANewDiscountStartsAllowingIt(): void
    {
        self::assertTrue($this->getProvider()->getDefaultData()['value']['allow_partial_use']);
    }

    private function getProvider(): DiscountFormDataProvider
    {
        $this->bootWithShopContext();

        return self::getContainer()->get(DiscountFormDataProvider::class);
    }

    private function getHandler(): DiscountFormDataHandler
    {
        $this->bootWithShopContext();

        return self::getContainer()->get(DiscountFormDataHandler::class);
    }

    private function bootWithShopContext(): void
    {
        if (null === self::$kernel) {
            self::bootKernel();
        }

        $context = Context::getContext();
        $context->container = self::getContainer();
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->shop = new Shop(1);
    }

    private function createAmountDiscount(int $partialUse): int
    {
        $this->bootWithShopContext();

        $cartRule = new CartRule();
        $cartRule->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Amount discount'];
        $cartRule->quantity = 10;
        $cartRule->quantity_per_user = 1;
        $cartRule->date_from = date('Y-m-d H:i:s', strtotime('-1 day'));
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+1 year'));
        $cartRule->active = true;
        $cartRule->priority = 1;
        $cartRule->reduction_amount = 20.0;
        $cartRule->reduction_tax = true;
        $cartRule->reduction_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        // The parameter carries the stored tinyint, which is what the UPDATE below writes; the
        // property itself is declared bool.
        $cartRule->partial_use = (bool) $partialUse;
        $cartRule->add();

        // The rewritten form only reads discounts that carry one of its types.
        Db::getInstance()->execute(sprintf(
            'UPDATE `%scart_rule` SET `id_cart_rule_type` = %d, `partial_use` = %d WHERE `id_cart_rule` = %d',
            _DB_PREFIX_,
            self::CART_LEVEL_DISCOUNT_TYPE_ID,
            $partialUse,
            (int) $cartRule->id
        ));

        return (int) $cartRule->id;
    }
}
