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
use PrestaShop\PrestaShop\Core\Domain\Discount\DiscountSettings;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\DiscountFormDataHandler;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\DiscountFormDataProvider;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountConditionsType;
use PrestaShopBundle\Form\Admin\Sell\Discount\ProductConditionsType;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The cheapest product target is not offered when building a discount any more, but the cart rule still
 * stores it (reduction_product = -1) and the price engine still applies it, so shops carry such
 * discounts - the legacy voucher form writes them to this day. The form has to show that target and
 * give it back unchanged; when it does not, it reports "none", which is not a target this type may
 * have, and the discount can no longer be saved at all.
 */
class DiscountCheapestProductTest extends KernelTestCase
{
    private const PRODUCT_LEVEL_DISCOUNT_TYPE_ID = 4;
    private const HOME_CATEGORY_ID = 2;

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
     * @dataProvider getStoredTargetAndExpectedSelector
     */
    public function testTheStoredTargetReachesTheForm(int $storedTarget, string $expectedSelector): void
    {
        $discountId = $this->createProductLevelDiscount($storedTarget);

        $data = $this->getProvider()->getData($discountId);

        self::assertSame(
            $expectedSelector,
            $data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS]['children_selector']
        );
    }

    public function getStoredTargetAndExpectedSelector(): iterable
    {
        yield 'cheapest product' => [DiscountSettings::CHEAPEST_PRODUCT, ProductConditionsType::CHEAPEST_PRODUCT];
        yield 'no target' => [DiscountSettings::PRODUCTS_TOTAL, ProductConditionsType::NONE];
    }

    /**
     * Saving the discount without touching its target - renaming it, moving its dates - must leave the
     * target alone. This is the one that bites a merchant: without the fix the save is refused with
     * "Product discount must target at least one product".
     */
    public function testSavingTheDiscountKeepsTheTarget(): void
    {
        $discountId = $this->createProductLevelDiscount(DiscountSettings::CHEAPEST_PRODUCT);

        $data = $this->getProvider()->getData($discountId);
        $this->getHandler()->update($discountId, $data);

        self::assertSame(DiscountSettings::CHEAPEST_PRODUCT, $this->readStoredTarget($discountId));
    }

    /**
     * The other half: the target is shown so that it can also be changed. Moving to a product segment
     * has to take effect, or the fix would trade a rejected save for a value nobody can change.
     */
    public function testTheTargetCanStillBeReplaced(): void
    {
        $discountId = $this->createProductLevelDiscount(DiscountSettings::CHEAPEST_PRODUCT);

        $data = $this->getProvider()->getData($discountId);
        $productConditions = &$data['conditions'][DiscountConditionsType::PRODUCT_CONDITIONS];
        $productConditions['children_selector'] = ProductConditionsType::PRODUCT_SEGMENT;
        $productConditions[ProductConditionsType::PRODUCT_SEGMENT]['category'] = self::HOME_CATEGORY_ID;
        $productConditions[ProductConditionsType::PRODUCT_SEGMENT]['quantity'] = 1;
        unset($productConditions);
        $this->getHandler()->update($discountId, $data);

        self::assertSame(DiscountSettings::PRODUCT_SEGMENT, $this->readStoredTarget($discountId));
    }

    /**
     * @dataProvider getSelectorAndExpectedField
     */
    public function testTheFieldIsOfferedOnlyToADiscountThatAlreadyUsesIt(string $selector, bool $expected): void
    {
        $this->bootWithShopContext();

        $form = self::getContainer()->get('form.factory')->create(
            ProductConditionsType::class,
            ['children_selector' => $selector],
            ['discount_type' => DiscountType::PRODUCT_LEVEL]
        );

        self::assertSame($expected, $form->has(ProductConditionsType::CHEAPEST_PRODUCT));
        self::assertSame(
            $expected,
            in_array(ProductConditionsType::CHEAPEST_PRODUCT, $form->get('children_selector')->getConfig()->getOption('choices'), true)
        );
    }

    public function getSelectorAndExpectedField(): iterable
    {
        yield 'already targets the cheapest product' => [ProductConditionsType::CHEAPEST_PRODUCT, true];
        yield 'targets nothing' => [ProductConditionsType::NONE, false];
        yield 'targets a single product' => [ProductConditionsType::SPECIFIC_PRODUCTS, false];
    }

    private function readStoredTarget(int $discountId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT reduction_product FROM ' . _DB_PREFIX_ . 'cart_rule WHERE id_cart_rule = ' . $discountId
        );
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

    private function createProductLevelDiscount(int $reductionProduct): int
    {
        $this->bootWithShopContext();

        $cartRule = new CartRule();
        $cartRule->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Cheapest product discount'];
        $cartRule->quantity = 10;
        $cartRule->quantity_per_user = 1;
        $cartRule->date_from = date('Y-m-d H:i:s', strtotime('-1 day'));
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+1 year'));
        $cartRule->active = true;
        $cartRule->priority = 1;
        $cartRule->reduction_percent = 10.0;
        $cartRule->reduction_product = $reductionProduct;
        $cartRule->add();

        // The rewritten form only reads discounts that carry one of its types.
        Db::getInstance()->execute(sprintf(
            'UPDATE `%scart_rule` SET `id_cart_rule_type` = %d WHERE `id_cart_rule` = %d',
            _DB_PREFIX_,
            self::PRODUCT_LEVEL_DISCOUNT_TYPE_ID,
            (int) $cartRule->id
        ));

        return (int) $cartRule->id;
    }
}
