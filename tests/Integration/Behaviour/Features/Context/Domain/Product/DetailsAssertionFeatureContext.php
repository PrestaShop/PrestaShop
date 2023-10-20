<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
