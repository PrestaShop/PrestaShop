<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Controllers\Front;

use Cache;
use Cart;
use Configuration;
use Context;
use Currency;
use Customer;
use Db;
use Language;
use PrestaShop\PrestaShop\Adapter\Presenter\Object\ObjectPresenter;
use Product;
use ProductControllerCore;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Product::loadStockData() reads StockAvailable for the product level row only, so the front office
 * reported that one location for every combination of a product - the wrong one, or none at all when
 * only the combinations carry a location.
 */
class ProductStockLocationTest extends KernelTestCase
{
    private const PRODUCT_ID = 1;

    /** @var array<int, string> */
    private array $previousLocations = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        foreach ($this->stockRows() as $row) {
            $this->previousLocations[(int) $row['id_product_attribute']] = (string) $row['location'];
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->previousLocations as $idProductAttribute => $location) {
            $this->setLocation($idProductAttribute, $location);
        }
        $this->previousLocations = [];

        parent::tearDown();
    }

    public function testTheSelectedCombinationsOwnLocationIsUsed(): void
    {
        $combinations = $this->combinationIds();
        $this->setLocation(0, 'BASE-ROW');
        $this->setLocation($combinations[0], 'COMBINATION-ONE');
        $this->setLocation($combinations[1], 'COMBINATION-TWO');

        $this->assertSame('COMBINATION-ONE', $this->presentedLocation($combinations[0]));
        $this->assertSame('COMBINATION-TWO', $this->presentedLocation($combinations[1]));
    }

    public function testACombinationWithoutItsOwnLocationFallsBackToTheProduct(): void
    {
        $combinations = $this->combinationIds();
        $this->setLocation(0, 'BASE-ROW');
        $this->setLocation($combinations[0], '');

        $this->assertSame('BASE-ROW', $this->presentedLocation($combinations[0]));
    }

    public function testNothingIsInventedWhenNoLocationIsSetAtAll(): void
    {
        $combinations = $this->combinationIds();
        $this->setLocation(0, '');
        $this->setLocation($combinations[0], '');

        $this->assertSame('', $this->presentedLocation($combinations[0]));
    }

    /**
     * The location the front office template would print for a given combination.
     */
    private function presentedLocation(int $idProductAttribute): string
    {
        $_GET['id_product_attribute'] = $idProductAttribute;
        $_POST['id_product_attribute'] = $idProductAttribute;

        $context = Context::getContext();
        $context->customer = new Customer();
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->cart = new Cart();
        $context->container = self::getContainer();
        $context->currentLocale = self::getContainer()
            ->get('prestashop.core.localization.locale.repository')
            ->getLocale($context->language->getLocale());

        $controller = new class() extends ProductControllerCore {
            public function loadProduct(int $idProduct): void
            {
                $this->product = new Product($idProduct, true, (int) Configuration::get('PS_LANG_DEFAULT'));
                $this->objectPresenter = new ObjectPresenter();
            }
        };
        $controller->loadProduct(self::PRODUCT_ID);

        fwrite(STDERR, sprintf(
            "\n  DEBUG pa=%d getLocation(direct)=%s db=%s\n",
            $idProductAttribute,
            var_export(StockAvailable::getLocation(self::PRODUCT_ID, $idProductAttribute), true),
            var_export(Db::getInstance()->getValue('SELECT location FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_product=' . self::PRODUCT_ID . ' AND id_product_attribute=' . $idProductAttribute, false), true)
        ));

        return (string) $controller->getTemplateVarProduct()['location'];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function stockRows(): array
    {
        return Db::getInstance()->executeS(
            'SELECT id_product_attribute, location FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . self::PRODUCT_ID
        ) ?: [];
    }

    /**
     * @return int[]
     */
    private function combinationIds(): array
    {
        $ids = [];
        foreach ($this->stockRows() as $row) {
            if ((int) $row['id_product_attribute'] > 0) {
                $ids[] = (int) $row['id_product_attribute'];
            }
        }

        if (count($ids) < 2) {
            $this->markTestSkipped('Two combinations are needed to tell them apart.');
        }

        return $ids;
    }

    private function setLocation(int $idProductAttribute, string $location): void
    {
        StockAvailable::setLocation(self::PRODUCT_ID, $location, null, $idProductAttribute);

        /*
         * Product::getProductProperties() memoises the whole presented row per
         * product/combination/language, and array_merges the cached copy OVER the caller's, so without
         * this a later case in the same process reads the previous one's location.
         */
        Product::resetStaticCache();
        Cache::clean('*');

        $stored = (string) Db::getInstance()->getValue(
            'SELECT location FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . self::PRODUCT_ID . ' AND id_product_attribute = ' . $idProductAttribute,
            false
        );
        $this->assertSame($location, $stored, 'the fixture must actually be written');
    }
}
