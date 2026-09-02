<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductOptions;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * Context for product assertions related to Options properties
 */
class OptionsAssertionFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Transform table:product option,value
     *
     * @param TableNode $tableNode
     *
     * @return ProductOptions
     */
    public function transformOptions(TableNode $tableNode): ProductOptions
    {
        $dataRows = $tableNode->getRowsHash();

        return new ProductOptions(
            $dataRows['visibility'],
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['available_for_order']),
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['online_only']),
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['show_price']),
            $dataRows['condition'],
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['show_condition']),
            $this->getManufacturerId($dataRows['manufacturer'])
        );
    }

    /**
     * @Then product :productReference should have following options:
     *
     * @param string $productReference
     * @param ProductOptions $expectedOptions
     */
    public function assertOptionsForDefaultShop(string $productReference, ProductOptions $expectedOptions): void
    {
        $this->assertOptions($productReference, $expectedOptions, $this->getDefaultShopId());
    }

    /**
     * @Then product :productReference should have following options for shops :shopReferences:
     *
     * @param string $productReference
     * @param ProductOptions $expectedOptions
     * @param string $shopReferences
     */
    public function assertOptionsForShops(string $productReference, ProductOptions $expectedOptions, string $shopReferences): void
    {
        $shopReferences = explode(',', $shopReferences);
        foreach ($shopReferences as $shopReference) {
            $shopId = $this->getSharedStorage()->get(trim($shopReference));
            $this->assertOptions($productReference, $expectedOptions, $shopId);
        }
    }

    /**
     * @param string $productReference
     * @param ProductOptions $expectedOptions
     * @param int $shopId
     */
    private function assertOptions(string $productReference, ProductOptions $expectedOptions, int $shopId): void
    {
        $properties = [
            'availableForOrder',
            'onlineOnly',
            'showPrice',
            'visibility',
            'condition',
            'showCondition',
            'manufacturerId',
        ];
        $actualOptions = $this->getProductForEditing($productReference, $shopId)->getOptions();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($properties as $property) {
            Assert::assertSame(
                $propertyAccessor->getValue($expectedOptions, $property),
                $propertyAccessor->getValue($actualOptions, $property),
                sprintf('Unexpected %s of product "%s"', $property, $productReference)
            );
        }
    }
}
