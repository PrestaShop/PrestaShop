<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use SpecificPrice;
use SpecificPriceRule;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * A catalog price rule that carries no condition is materialised as a single specific_price row with
 * id_product = 0, standing for every product of the shop. getProductIdByDate() handed that sentinel to
 * its callers, which filter products on it, so a shop-wide rule discounted products everywhere except
 * on the pages built from this list.
 */
class SpecificPriceGlobalRuleExpansionTest extends TestCase
{
    use ContextMockerTrait;
    private const RULE_NAME = 'test-global-rule-expansion';

    /** @var int */
    private $shopId;

    /** @var string */
    private $now;

    /** @var string|false */
    private $featureWasActive;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $this->shopId = (int) self::getContext()->shop->id;
        $this->now = date('Y-m-d H:i:00');

        $this->featureWasActive = Configuration::get('PS_SPECIFIC_PRICE_FEATURE_ACTIVE');
        Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', 1);

        $this->removeRule();
    }

    protected function tearDown(): void
    {
        $this->removeRule();
        Configuration::updateGlobalValue('PS_SPECIFIC_PRICE_FEATURE_ACTIVE', $this->featureWasActive);

        parent::tearDown();
    }

    /**
     * The sentinel is what the rule really writes — this is the precondition the rest of the test
     * relies on, so it is asserted rather than assumed.
     */
    public function testAShopWideRuleIsStoredAsASingleSentinelRow(): void
    {
        $rule = $this->applyGlobalRule();

        $rows = Db::getInstance()->executeS(
            'SELECT id_product FROM ' . _DB_PREFIX_ . 'specific_price WHERE id_specific_price_rule = ' . (int) $rule->id
        );

        self::assertCount(1, $rows, 'a rule without conditions writes one row, not one per product');
        self::assertSame(0, (int) $rows[0]['id_product'], 'that row carries the id_product = 0 sentinel');
    }

    public function testTheSentinelIsNeverHandedToCallers(): void
    {
        self::assertNotContains(0, $this->getIds(), 'no rule, no sentinel');

        $this->applyGlobalRule();

        self::assertNotContains(0, $this->getIds(), 'id_product = 0 matches no product, so it must not be returned');
    }

    public function testEveryProductOfTheShopIsReturnedUnderAShopWideRule(): void
    {
        $this->applyGlobalRule();
        $ids = $this->getIds();

        $missing = array_diff($this->getShopProductIds(), $ids);

        self::assertSame([], array_values($missing), 'a shop-wide rule discounts every product of the shop');
    }

    /**
     * The reported symptom: a product with no specific price of its own was left out.
     */
    public function testAProductWithoutItsOwnSpecificPriceIsIncluded(): void
    {
        $untouched = (int) Db::getInstance()->getValue(
            'SELECT ps.id_product FROM ' . _DB_PREFIX_ . 'product_shop ps
             WHERE ps.id_shop = ' . $this->shopId . '
               AND ps.id_product NOT IN (SELECT id_product FROM ' . _DB_PREFIX_ . 'specific_price)
             ORDER BY ps.id_product'
        );
        self::assertGreaterThan(0, $untouched, 'fixture needs a product with no specific price of its own');

        self::assertNotContains($untouched, $this->getIds(), 'precondition: not discounted before the rule');

        $this->applyGlobalRule();

        self::assertContains($untouched, $this->getIds(), 'the shop-wide rule discounts it too');
    }

    /**
     * The expansion has to stay inside the shop the sentinel row belongs to.
     */
    public function testTheExpansionIsScopedToTheShopTheRuleBelongsTo(): void
    {
        $otherShop = 1 + (int) Db::getInstance()->getValue('SELECT MAX(id_shop) FROM ' . _DB_PREFIX_ . 'shop');
        $before = $this->getIds($otherShop);

        $this->applyGlobalRule();

        self::assertSame($before, $this->getIds($otherShop), 'a rule of another shop must not expand here');
    }

    /**
     * getRandomSpecial() asks for combinations; the expanded entries stand for the whole product.
     */
    public function testExpandedEntriesCarryNoCombination(): void
    {
        $this->applyGlobalRule();

        $rows = SpecificPrice::getProductIdByDate(
            $this->shopId, 0, (int) Configuration::get('PS_COUNTRY_DEFAULT'), 1, $this->now, $this->now, 0, true
        );

        $shopProducts = $this->getShopProductIds();
        $seen = [];
        foreach ($rows as $row) {
            self::assertArrayHasKey('id_product_attribute', $row);
            if (in_array((int) $row['id_product'], $shopProducts, true) && (int) $row['id_product_attribute'] === 0) {
                $seen[] = (int) $row['id_product'];
            }
        }

        self::assertSame([], array_values(array_diff($shopProducts, $seen)));
    }

    /**
     * @return array<int>
     */
    private function getIds(?int $shopId = null): array
    {
        SpecificPrice::resetStaticCache();

        return array_map('intval', SpecificPrice::getProductIdByDate(
            $shopId ?? $this->shopId,
            0,
            (int) Configuration::get('PS_COUNTRY_DEFAULT'),
            1,
            $this->now,
            $this->now
        ));
    }

    /**
     * @return array<int>
     */
    private function getShopProductIds(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT id_product FROM ' . _DB_PREFIX_ . 'product_shop WHERE id_shop = ' . $this->shopId
        );

        return array_map(function ($row) { return (int) $row['id_product']; }, $rows);
    }

    private function applyGlobalRule(): SpecificPriceRule
    {
        $rule = new SpecificPriceRule();
        $rule->name = self::RULE_NAME;
        $rule->id_shop = $this->shopId;
        $rule->id_currency = 0;
        $rule->id_country = 0;
        $rule->id_group = 0;
        $rule->from_quantity = 1;
        $rule->price = -1;
        $rule->reduction = 10;
        $rule->reduction_tax = 1;
        $rule->reduction_type = 'percentage';
        $rule->from = '0000-00-00 00:00:00';
        $rule->to = '0000-00-00 00:00:00';
        $rule->add();
        $rule->apply();

        return $rule;
    }

    private function removeRule(): void
    {
        $rows = Db::getInstance()->executeS(
            'SELECT id_specific_price_rule FROM ' . _DB_PREFIX_ . 'specific_price_rule WHERE name = "' . self::RULE_NAME . '"'
        );
        foreach ($rows as $row) {
            (new SpecificPriceRule((int) $row['id_specific_price_rule']))->delete();
        }
    }
}
