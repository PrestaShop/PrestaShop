<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\QueryHandler;

use Configuration;
use Context;
use Currency;
use Group;
use Order;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderProductsForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The order view lists a single unit price per product, picked by the customer group's
 * price display method. These tests cover the counterpart value that carries the tax
 * method the order does not display.
 */
class GetOrderProductsForViewingHandlerTest extends KernelTestCase
{
    /**
     * More decimals than any currency displays, so that "was it rounded" stays visible
     * in the formatted output instead of being hidden by the formatter.
     */
    private const UNIT_PRICE_TAX_EXCL = '41.996';
    private const UNIT_PRICE_TAX_INCL = '49.996';

    private $queryBus;

    private $connection;

    private $locale;

    private int $orderId;

    private int $orderDetailId;

    private int $groupId;

    private string $currencyIsoCode;

    /**
     * @var array<string, string>
     */
    private array $backup = [];

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();
        // The legacy objects this handler builds reach for the container through the context,
        // and Context::getComputingPrecision() reads a currency a CLI run has none of.
        Context::getContext()->container = $container;
        Context::getContext()->currency = Currency::getDefaultCurrency();

        $this->queryBus = $container->get('prestashop.core.query_bus');
        $this->connection = $container->get('doctrine.dbal.default_connection');
        $this->locale = $container->get('prestashop.core.localization.locale.context_locale');

        // A taxed product is required: at a 0 tax rate both tax methods yield the same
        // number and every assertion below would hold vacuously.
        $row = $this->connection->executeQuery(
            'SELECT od.id_order_detail, od.id_order, o.id_currency, c.id_default_group
               FROM ' . _DB_PREFIX_ . 'order_detail od
               INNER JOIN ' . _DB_PREFIX_ . 'orders o ON o.id_order = od.id_order
               INNER JOIN ' . _DB_PREFIX_ . 'customer c ON c.id_customer = o.id_customer
              WHERE od.unit_price_tax_excl <> od.unit_price_tax_incl
              ORDER BY od.id_order_detail
              LIMIT 1'
        )->fetchAssociative();

        if (!$row) {
            self::markTestSkipped('The fixtures hold no taxed order detail to measure.');
        }

        $this->orderDetailId = (int) $row['id_order_detail'];
        $this->orderId = (int) $row['id_order'];
        $this->groupId = (int) $row['id_default_group'];
        $this->currencyIsoCode = (new Currency((int) $row['id_currency']))->iso_code;

        $this->backup = [
            'roundType' => (string) $this->connection->executeQuery(
                'SELECT round_type FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->orderId
            )->fetchOne(),
            'priceDisplayMethod' => (string) $this->connection->executeQuery(
                'SELECT price_display_method FROM ' . _DB_PREFIX_ . 'group WHERE id_group = ' . $this->groupId
            )->fetchOne(),
            'unitPriceTaxExcl' => (string) $this->connection->executeQuery(
                'SELECT unit_price_tax_excl FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order_detail = ' . $this->orderDetailId
            )->fetchOne(),
            'unitPriceTaxIncl' => (string) $this->connection->executeQuery(
                'SELECT unit_price_tax_incl FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order_detail = ' . $this->orderDetailId
            )->fetchOne(),
            'priceRoundMode' => (string) Configuration::get('PS_PRICE_ROUND_MODE'),
        ];

        $this->writeUnitPrices(self::UNIT_PRICE_TAX_EXCL, self::UNIT_PRICE_TAX_INCL);
        $this->writeRoundType(Order::ROUND_ITEM);
        // Floor rounding parts company with the formatter's own rounding, which is what
        // makes the rounding of the counterpart observable at all.
        Configuration::updateValue('PS_PRICE_ROUND_MODE', PS_ROUND_DOWN);
    }

    protected function tearDown(): void
    {
        if ($this->backup) {
            $this->writeUnitPrices($this->backup['unitPriceTaxExcl'], $this->backup['unitPriceTaxIncl']);
            $this->writeRoundType((int) $this->backup['roundType']);
            $this->writePriceDisplayMethod((int) $this->backup['priceDisplayMethod']);
            Configuration::updateValue('PS_PRICE_ROUND_MODE', (int) $this->backup['priceRoundMode']);
            $this->backup = [];
        }

        parent::tearDown();
    }

    public function testItExposesTheUnitPriceInTheTaxMethodTheOrderDoesNotDisplay(): void
    {
        $taxExcluded = $this->getProductForPriceDisplayMethod(Group::PRICE_DISPLAY_METHOD_TAX_EXCL);
        $taxIncluded = $this->getProductForPriceDisplayMethod(Group::PRICE_DISPLAY_METHOD_TAX_INCL);

        // Guards everything below: without this the two runs could be identical and the
        // swap assertions would prove nothing.
        $this->assertNotSame($taxExcluded->getUnitPrice(), $taxIncluded->getUnitPrice());

        $this->assertSame($taxIncluded->getUnitPrice(), $taxExcluded->getUnitPriceOtherTaxMethod());
        $this->assertSame($taxExcluded->getUnitPrice(), $taxIncluded->getUnitPriceOtherTaxMethod());
    }

    public function testItRoundsTheCounterpartLikeTheDisplayedPriceWhenRoundingPerItem(): void
    {
        $product = $this->getProductForPriceDisplayMethod(Group::PRICE_DISPLAY_METHOD_TAX_EXCL);

        // Both values go through the order's own rounding, so the secondary price
        // reconciles with the invoice instead of being rounded by the formatter alone.
        $this->assertSame(
            $this->locale->formatPrice('41.99', $this->currencyIsoCode),
            $product->getUnitPrice()
        );
        $this->assertSame(
            $this->locale->formatPrice('49.99', $this->currencyIsoCode),
            $product->getUnitPriceOtherTaxMethod()
        );
    }

    private function getProductForPriceDisplayMethod(int $priceDisplayMethod): OrderProductForViewing
    {
        $this->writePriceDisplayMethod($priceDisplayMethod);

        $products = $this->queryBus->handle(GetOrderProductsForViewing::all($this->orderId))->getProducts();
        foreach ($products as $product) {
            if ($product->getOrderDetailId() === $this->orderDetailId) {
                return $product;
            }
        }

        self::fail(sprintf('Order detail %d is missing from the order view.', $this->orderDetailId));
    }

    private function writePriceDisplayMethod(int $priceDisplayMethod): void
    {
        $this->connection->executeStatement(
            'UPDATE ' . _DB_PREFIX_ . 'group SET price_display_method = :method WHERE id_group = :group',
            ['method' => $priceDisplayMethod, 'group' => $this->groupId]
        );
        // getPriceDisplayMethod() memoises per group for the whole process.
        Group::clearCachedValues();
    }

    private function writeRoundType(int $roundType): void
    {
        $this->connection->executeStatement(
            'UPDATE ' . _DB_PREFIX_ . 'orders SET round_type = :type WHERE id_order = :order',
            ['type' => $roundType, 'order' => $this->orderId]
        );
    }

    private function writeUnitPrices(string $taxExcluded, string $taxIncluded): void
    {
        $this->connection->executeStatement(
            'UPDATE ' . _DB_PREFIX_ . 'order_detail
                SET unit_price_tax_excl = :excl, unit_price_tax_incl = :incl
              WHERE id_order_detail = :detail',
            ['excl' => $taxExcluded, 'incl' => $taxIncluded, 'detail' => $this->orderDetailId]
        );
    }
}
