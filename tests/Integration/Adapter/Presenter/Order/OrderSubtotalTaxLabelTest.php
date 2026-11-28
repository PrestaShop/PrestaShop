<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Presenter\Order;

use Configuration;
use Db;
use Group;
use Order;
use PrestaShop\PrestaShop\Adapter\Presenter\Order\OrderSubtotalLazyArray;
use Product;
use ReflectionProperty;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The order summary lists a tax amount next to prices that follow the customer group's price display
 * method. Naming that row "Tax" leaves a tax-excluded group unable to tell whether the amount is
 * already inside the prices above it or added to them.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/23784
 */
class OrderSubtotalTaxLabelTest extends KernelTestCase
{
    private static Order $order;

    /** @var array<string, string> configuration values as found before this test class ran */
    private static array $initialConfiguration = [];

    private static int $groupId;

    private static int $initialPriceDisplayMethod;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();
        // The presenter resolves the translator through the legacy container finder.
        global $kernel;
        $kernel = self::$kernel;

        // An order whose tax-included and tax-excluded totals differ, so the tax row has a value.
        $orderId = (int) Db::getInstance()->getValue(
            'SELECT MIN(`id_order`) FROM `' . _DB_PREFIX_ . 'orders` WHERE `total_paid_tax_incl` > `total_paid_tax_excl`'
        );
        self::$order = new Order($orderId);

        foreach (['PS_TAX', 'PS_TAX_DISPLAY'] as $key) {
            self::$initialConfiguration[$key] = (string) Configuration::get($key);
            Configuration::updateValue($key, 1);
        }

        self::$groupId = (int) Group::getCurrent()->id;
        self::$initialPriceDisplayMethod = Group::getPriceDisplayMethod(self::$groupId);
    }

    protected function setUp(): void
    {
        parent::setUp();
        // PHPUnit shuts the kernel down between tests; the legacy container finder needs it back.
        global $kernel;
        $kernel = static::bootKernel();
    }

    public static function tearDownAfterClass(): void
    {
        self::setPriceDisplayMethod(self::$initialPriceDisplayMethod);

        foreach (self::$initialConfiguration as $key => $value) {
            Configuration::updateValue($key, $value);
        }

        parent::tearDownAfterClass();
    }

    public function testTaxRowIsNamedAfterAnAdditionForATaxExcludedGroup(): void
    {
        self::setPriceDisplayMethod(PS_TAX_EXC);

        $subtotals = new OrderSubtotalLazyArray(self::$order);

        $this->assertGreaterThan(0, $subtotals['tax']['amount']);
        $this->assertSame('Taxes', $subtotals['tax']['label']);
    }

    public function testTaxRowIsNamedAfterAnInclusionForATaxIncludedGroup(): void
    {
        self::setPriceDisplayMethod(PS_TAX_INC);

        $subtotals = new OrderSubtotalLazyArray(self::$order);

        $this->assertGreaterThan(0, $subtotals['tax']['amount']);
        $this->assertSame('Included taxes', $subtotals['tax']['label']);
    }

    public function testTaxRowStaysEmptyWhenTheShopDoesNotDisplayItInTheCart(): void
    {
        Configuration::updateValue('PS_TAX_DISPLAY', 0);

        try {
            $subtotals = new OrderSubtotalLazyArray(self::$order);

            $this->assertNull($subtotals['tax']['label']);
        } finally {
            Configuration::updateValue('PS_TAX_DISPLAY', 1);
        }
    }

    /**
     * Both the group's price display method and the derived tax calculation method are memoised in
     * static properties that outlive a single request, so they have to be dropped alongside the write.
     */
    private static function setPriceDisplayMethod(int $priceDisplayMethod): void
    {
        Db::getInstance()->update('group', ['price_display_method' => $priceDisplayMethod], 'id_group = ' . self::$groupId);

        $groupCache = new ReflectionProperty(Group::class, 'group_price_display_method');
        $groupCache->setAccessible(true);
        $groupCache->setValue(null, []);

        $productCache = new ReflectionProperty(Product::class, '_taxCalculationMethod');
        $productCache->setAccessible(true);
        $productCache->setValue(null, null);
    }
}
