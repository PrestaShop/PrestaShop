<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Grid\Data\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Product\Grid\Data\Factory\ProductGridDataFactoryDecorator;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Pack\Repository\ProductPackRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\ShopSearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A back office whose employee language holds no product data used to show "N/A" on every row of the
 * product grid, because the grid reads product_lang with that language and nothing else. The rows
 * that come back without a name are now looked up once in the shop's default language.
 */
class ProductGridNameFallbackTest extends TestCase
{
    private const DEFAULT_LANGUAGE_ID = 1;
    private const SHOP_ID = 2;

    /**
     * @var ProductRepository&MockObject
     */
    private $productRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = $this->createMock(ProductRepository::class);
    }

    public function testTheDefaultLanguageNameReplacesAnEmptyOne(): void
    {
        $this->productRepository
            ->expects($this->once())
            ->method('getProductNames')
            ->with([7], self::DEFAULT_LANGUAGE_ID, self::SHOP_ID)
            ->willReturn([7 => 'Hummingbird printed t-shirt']);

        $products = $this->fillNames([
            ['id_product' => 5, 'name' => 'Mug'],
            ['id_product' => 7, 'name' => ''],
        ]);

        $this->assertSame('Mug', $products[0]['name']);
        $this->assertSame('Hummingbird printed t-shirt', $products[1]['name']);
    }

    /**
     * A shop whose data is complete must not pay for the lookup at all.
     */
    public function testNoLookupHappensWhenEveryRowHasAName(): void
    {
        $this->productRepository->expects($this->never())->method('getProductNames');

        $products = $this->fillNames([
            ['id_product' => 5, 'name' => 'Mug'],
            ['id_product' => 7, 'name' => 'Notebook'],
        ]);

        $this->assertSame(['Mug', 'Notebook'], array_column($products, 'name'));
    }

    /**
     * When the default language has nothing either, the row is left empty so that the caller still
     * renders "N/A" rather than something invented here.
     */
    public function testARowStaysEmptyWhenTheDefaultLanguageHasNoNameEither(): void
    {
        $this->productRepository
            ->method('getProductNames')
            ->willReturn([]);

        $products = $this->fillNames([
            ['id_product' => 7, 'name' => ''],
        ]);

        $this->assertSame('', $products[0]['name']);
    }

    /**
     * @param array<int, array<string, mixed>> $products
     *
     * @return array<int, array<string, mixed>>
     */
    private function fillNames(array $products): array
    {
        $configuration = $this->createMock(Configuration::class);
        $configuration->method('getInt')->with('PS_LANG_DEFAULT')->willReturn(self::DEFAULT_LANGUAGE_ID);

        $localeRepository = $this->createMock(LocaleRepository::class);
        $localeRepository->method('getLocale')->willReturn(
            $this->createMock(\PrestaShop\PrestaShop\Core\Localization\Locale::class)
        );

        $decorator = new TestableProductGridDataFactoryDecorator(
            $this->createMock(GridDataFactoryInterface::class),
            $localeRepository,
            'en-US',
            1,
            $this->createMock(TaxComputer::class),
            1,
            $this->createMock(ProductImagePathFactory::class),
            $this->createMock(TranslatorInterface::class),
            true,
            false,
            0,
            $this->createMock(ShopRepository::class),
            $this->productRepository,
            $this->createMock(ProductPackRepository::class),
            $configuration
        );

        $searchCriteria = $this->createMock(ShopSearchCriteriaInterface::class);
        $searchCriteria->method('getShopConstraint')->willReturn(ShopConstraint::shop(self::SHOP_ID));

        return $decorator->fillNames($products, $searchCriteria);
    }
}

class TestableProductGridDataFactoryDecorator extends ProductGridDataFactoryDecorator
{
    /**
     * @param array<int, array<string, mixed>> $products
     *
     * @return array<int, array<string, mixed>>
     */
    public function fillNames(array $products, ShopSearchCriteriaInterface $searchCriteria): array
    {
        return $this->fillNamesMissingInTheEmployeeLanguage($products, $searchCriteria);
    }
}
