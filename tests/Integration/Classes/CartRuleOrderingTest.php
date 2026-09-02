<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Classes;

use Cart;
use CartRule;
use Configuration;
use Context;
use Currency;
use Db;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartRuleOrderingTest extends KernelTestCase
{
    /** @var Cart|null */
    private $cart;

    /** @var CartRule[] */
    private $cartRules = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Cart::getCartRules() resolves the discount feature flag through ContainerFinder, which reads
        // the global $kernel that bootKernel() does not set by itself.
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        $context = Context::getContext();
        // Cart price computation reads the currency precision off the context.
        $context->currency = Currency::getDefaultCurrency();

        $this->cart = new Cart();
        $this->cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $this->cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->cart->id_shop = (int) $context->shop->id;
        $this->cart->add();
    }

    protected function tearDown(): void
    {
        foreach ($this->cartRules as $cartRule) {
            $cartRule->delete();
        }
        $this->cartRules = [];

        if ($this->cart && $this->cart->id) {
            Db::getInstance()->delete('cart_cart_rule', 'id_cart = ' . (int) $this->cart->id);
            $this->cart->delete();
        }
        $this->cart = null;

        parent::tearDown();
    }

    /**
     * Every rule a merchant creates without touching the priority field shares the schema default of 1,
     * so ordering on priority alone leaves them tied and the database free to return any order. The
     * reported symptom is a discount dropping to 0 when the row set changes and the order flips.
     */
    public function testRulesOfEqualPriorityAreOrderedByCreationDate(): void
    {
        // Created in one order, dated in the opposite one, so id order and date order disagree.
        $names = ['first created', 'second created', 'third created'];
        $ids = [];

        foreach ($names as $name) {
            $cartRule = new CartRule();
            $cartRule->id_customer = 0;
            $cartRule->date_from = date('Y-m-d H:i:s', strtotime('-1 day'));
            $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+1 day'));
            $cartRule->quantity = 10;
            $cartRule->quantity_per_user = 10;
            $cartRule->reduction_percent = 5;
            $cartRule->active = 1;
            $cartRule->name = [(int) Configuration::get('PS_LANG_DEFAULT') => $name];
            $cartRule->add();

            $this->cartRules[] = $cartRule;
            $ids[] = (int) $cartRule->id;

            Db::getInstance()->insert('cart_cart_rule', [
                'id_cart' => (int) $this->cart->id,
                'id_cart_rule' => (int) $cartRule->id,
            ]);
        }

        $this->assertSame(
            [1, 1, 1],
            array_map(static function (CartRule $rule): int { return (int) $rule->priority; }, $this->cartRules),
            'the rules must share a priority for this to test anything'
        );

        // Date them in reverse, so following the dates cannot be mistaken for following the ids.
        foreach (array_reverse($ids) as $offset => $idCartRule) {
            Db::getInstance()->update(
                'cart_rule',
                ['date_add' => date('Y-m-d H:i:s', strtotime(sprintf('-%d hours', 10 - $offset)))],
                'id_cart_rule = ' . (int) $idCartRule
            );
        }

        $cart = new Cart((int) $this->cart->id);

        $returned = array_map(
            static function (array $row): int { return (int) $row['id_cart_rule']; },
            // Second argument off: automatically adding rules needs the kernel container.
            $cart->getCartRules(CartRule::FILTER_ACTION_ALL, false)
        );

        $this->assertSame(
            array_reverse($ids),
            $returned,
            'equal priorities must fall back to the creation date, not to whatever the database returns'
        );
    }
}
