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
