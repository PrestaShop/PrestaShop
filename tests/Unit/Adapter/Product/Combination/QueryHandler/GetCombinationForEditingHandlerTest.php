<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Product\Combination\QueryHandler;

use Combination;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Adapter\Attribute\Repository\AttributeRepository;
use PrestaShop\PrestaShop\Adapter\Product\Combination\QueryHandler\GetCombinationForEditingHandler;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Repository\StockAvailableRepository;
use PrestaShop\PrestaShop\Adapter\Tax\TaxComputer;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Product\Combination\NameBuilder\CombinationNameBuilderInterface;
use PrestaShop\PrestaShop\Core\Util\Number\NumberExtractor;
use Product;
use StockAvailable;

class GetCombinationForEditingHandlerTest extends TestCase
{
    /**
     * @dataProvider provideShopConstraints
     */
    public function testPricesUseQueryShopConstraint(ShopConstraint $shopConstraint, bool $taxEnabled): void
    {
        $combination = $this->createMock(Combination::class);
        $combination->id = 1;
        $combination->id_product = 1;
        $combination->method('getShopId')->willReturn(2);
        $combinationRepository = $this->createMock(CombinationRepository::class);
        $combinationRepository->method('getByShopConstraint')->willReturn($combination);

        $product = $this->createMock(Product::class);
        $product->id_tax_rules_group = 1;
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('getByShopConstraint')->willReturn($product);

        $attributeRepository = $this->createMock(AttributeRepository::class);
        $attributeRepository->method('getAttributesInfoByCombinationIds')->willReturn([1 => []]);
        $stock = $this->createMock(StockAvailable::class);
        $stock->location = '';
        $stockRepository = $this->createMock(StockAvailableRepository::class);
        $stockRepository->method('getForCombination')->willReturn($stock);
        $numberExtractor = $this->createMock(NumberExtractor::class);
        $numberExtractor->method('extract')->willReturn(new DecimalNumber('10'));

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->expects($this->exactly(3))->method('get')->willReturnCallback(
            function (string $key, $default, ShopConstraint $constraint) use ($shopConstraint, $taxEnabled) {
                self::assertTrue($shopConstraint->isEqual($constraint), $key . ' must use the query shop constraint.');

                return match ($key) {
                    'PS_TAX' => $taxEnabled,
                    'PS_ECOTAX_TAX_RULES_GROUP_ID' => 2,
                    'PS_COUNTRY_DEFAULT' => 21,
                    default => throw new InvalidArgumentException('Unexpected configuration key: ' . $key),
                };
            }
        );

        $taxComputer = $this->createMock(TaxComputer::class);
        if ($taxEnabled) {
            $taxComputer->expects($this->exactly(3))->method('computePriceWithTaxes')->willReturnCallback(
                function (DecimalNumber $price, TaxRulesGroupId $taxGroupId, CountryId $countryId): DecimalNumber {
                    self::assertSame(21, $countryId->getValue());
                    self::assertSame('10', (string) $price);

                    return new DecimalNumber($taxGroupId->getValue() === 2 ? '15' : '12');
                }
            );
            $taxComputer->expects($this->once())->method('getTaxRate')
                ->with(new TaxRulesGroupId(1), new CountryId(21))
                ->willReturn(new DecimalNumber('20'));
        } else {
            $taxComputer->expects($this->never())->method('computePriceWithTaxes');
            $taxComputer->expects($this->never())->method('getTaxRate');
        }

        $handler = new GetCombinationForEditingHandler(
            $combinationRepository,
            $this->createMock(CombinationNameBuilderInterface::class),
            $stockRepository,
            $attributeRepository,
            $productRepository,
            $this->createMock(ProductImageRepository::class),
            $numberExtractor,
            $taxComputer,
            1,
            $configuration,
            $this->createMock(ProductImagePathFactory::class)
        );

        $prices = $handler->handle(new GetCombinationForEditing(1, $shopConstraint))->getPrices();
        self::assertSame($taxEnabled ? '12' : '10', (string) $prices->getImpactOnPriceTaxIncluded());
        self::assertSame($taxEnabled ? '12' : '10', (string) $prices->getImpactOnUnitPriceTaxIncluded());
        self::assertSame($taxEnabled ? '15' : '10', (string) $prices->getEcotaxTaxIncluded());
        self::assertSame($taxEnabled ? '20' : '0', (string) $prices->getProductTaxRate());
    }

    public static function provideShopConstraints(): iterable
    {
        yield 'all shops with taxes' => [ShopConstraint::allShops(), true];
        yield 'all shops without taxes' => [ShopConstraint::allShops(), false];
        yield 'specific shop with taxes' => [ShopConstraint::shop(2), true];
        yield 'specific shop without taxes' => [ShopConstraint::shop(2), false];
    }
}
