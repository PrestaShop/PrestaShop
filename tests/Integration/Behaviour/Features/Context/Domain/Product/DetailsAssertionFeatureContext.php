<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\QueryResult\ProductDetails;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Context for asserting product Details related properties
 */
class DetailsAssertionFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Transform table:product detail,value
     *
     * @param TableNode $tableNode
     *
     * @return ProductDetails
     */
    public function transformDetails(TableNode $tableNode): ProductDetails
    {
        $dataRows = $tableNode->getRowsHash();

        return new ProductDetails(
            $dataRows['isbn'],
            $dataRows['upc'],
            $dataRows['ean13'],
            $dataRows['mpn'],
            $dataRows['reference']
        );
    }

    /**
     * @Then product :productReference should have following details:
     *
     * @param string $productReference
     * @param ProductDetails $expectedDetails
     */
    public function assertDetailsForDefaultShop(string $productReference, ProductDetails $expectedDetails): void
    {
        $this->assertDetails($productReference, $expectedDetails, $this->getDefaultShopId());
    }

    /**
     * @Then product :productReference should have following details for shop(s) :shopReferences:
     *
     * @param string $productReference
     * @param string $shopReferences
     * @param ProductDetails $expectedDetails
     */
    public function assertDetailsForShops(string $productReference, string $shopReferences, ProductDetails $expectedDetails): void
    {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            $this->assertDetails($productReference, $expectedDetails, $shopId);
        }
    }

    private function assertDetails(string $productReference, ProductDetails $expectedDetails, int $shopId): void
    {
        $properties = ['ean13', 'isbn', 'mpn', 'reference', 'upc'];
        $actualDetails = $this->getProductForEditing($productReference, $shopId)->getDetails();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        foreach ($properties as $propertyName) {
            Assert::assertSame(
                $propertyAccessor->getValue($expectedDetails, $propertyName),
                $propertyAccessor->getValue($actualDetails, $propertyName),
                sprintf('Unexpected %s of "%s"', $propertyName, $productReference)
            );
        }
    }
}
