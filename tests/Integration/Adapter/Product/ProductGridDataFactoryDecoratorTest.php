<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Product\Grid\Data\Factory;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\Grid\Data\Factory\ProductGridDataFactoryDecorator;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Pack\Repository\ProductPackRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Grid\Data\Factory\GridDataFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Localization\Locale\Repository as LocaleRepository;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * The decorator reads grid rows straight out of the database layer, and whether that layer hands back
 * `1` or `'1'` depends on the driver: with native prepared statements the value arrives as an integer,
 * with emulated prepares (and on some MariaDB setups) every column arrives as a string. The typed
 * value objects it builds from those rows therefore have to cast, or the product list dies with a
 * TypeError on hosts that return strings.
 */
class ProductGridDataFactoryDecoratorTest extends KernelTestCase
{
    private const PACK_QUANTITY = 42;

    public function testItBuildsPackQuantityFromStringTypedProductId(): void
    {
        self::bootKernel();

        $stringTypedPackRow = [
            'id_product' => '7',
            'id_shop_default' => '1',
            'id_image' => '0',
            'legend' => '',
            'name' => 'A pack whose columns all came back as strings',
            'reference' => '',
            'price' => '10.000000',
            'id_tax_rules_group' => '0',
            'quantity' => '3',
            'product_type' => ProductType::TYPE_PACK,
        ];

        // The repository is stubbed so the assertion is about the value object the decorator builds,
        // not about which fixture products happen to be packs.
        $packRepository = $this->createMock(ProductPackRepository::class);
        $packRepository
            ->expects(self::once())
            ->method('getDynamicPackQuantity')
            ->with(
                self::callback(static fn (ProductId $productId): bool => $productId->getValue() === 7),
                self::anything()
            )
            ->willReturn(self::PACK_QUANTITY);

        $decorator = $this->buildDecorator($stringTypedPackRow, $packRepository);

        // Before the cast this threw:
        // ProductId::__construct(): Argument #1 ($productId) must be of type int, string given
        $gridData = $decorator->getData(new ProductFilters(ShopConstraint::shop(1)));

        $records = $gridData->getRecords()->all();
        self::assertCount(1, $records);
        self::assertSame(self::PACK_QUANTITY, $records[0]['quantity']);
    }

    private function buildDecorator(
        array $productRow,
        ProductPackRepository $packRepository
    ): ProductGridDataFactoryDecorator {
        $container = self::getContainer();

        $innerFactory = new class($productRow) implements GridDataFactoryInterface {
            public function __construct(private array $productRow)
            {
            }

            public function getData(SearchCriteriaInterface $searchCriteria): GridData
            {
                return new GridData(new RecordCollection([$this->productRow]), 1, '');
            }
        };

        $configuration = $container->get(Configuration::class);
        $legacyContext = $container->get(LegacyContext::class);

        return new ProductGridDataFactoryDecorator(
            $innerFactory,
            $container->get(LocaleRepository::class),
            $legacyContext->getContext()->language->getLocale(),
            (int) $configuration->get('PS_CURRENCY_DEFAULT'),
            $container->get(TaxComputer::class),
            (int) $legacyContext->getContext()->country->id,
            $container->get(ProductImagePathFactory::class),
            $container->get(TranslatorInterface::class),
            $configuration->getBoolean('PS_TAX'),
            $configuration->getBoolean('PS_USE_ECOTAX'),
            $configuration->getInt('PS_ECOTAX_TAX_RULES_GROUP_ID'),
            $container->get(ShopRepository::class),
            $container->get(ProductRepository::class),
            $packRepository,
        );
    }
}
