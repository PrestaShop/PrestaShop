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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\Combination;

use Behat\Gherkin\Node\TableNode;
use DateTime;
use Language;
use PHPUnit\Framework\Assert;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationDetails;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationPrices;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\QueryResult\CombinationStock;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use RuntimeException;
use StockAvailable;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class CombinationAssertionFeatureContext extends AbstractCombinationFeatureContext
{
    /**
     * @Then combination :combinationReference should have following details:
     *
     * @param string $combinationReference
     * @param CombinationDetails $expectedDetails
     */
    public function assertDetailsForDefaultShop(string $combinationReference, CombinationDetails $expectedDetails): void
    {
        $this->assertDetails($combinationReference, $expectedDetails, [$this->getDefaultShopId()]);
    }

    /**
     * @Then combination ":combinationReference" should have following details for shops ":shopReferences":
     *
     * @param string $combinationReference
     * @param CombinationDetails $expectedDetails
     */
    public function assertDetailsForShops(string $combinationReference, CombinationDetails $expectedDetails, string $shopReferences): void
    {
        $this->assertDetails($combinationReference, $expectedDetails, $this->referencesToIds($shopReferences));
    }

    /**
     * @Transform table:combination detail,value
     *
     * @param TableNode $tableNode
     *
     * @return CombinationDetails
     */
    public function transformDetails(TableNode $tableNode): CombinationDetails
    {
        $details = $tableNode->getRowsHash();

        return new CombinationDetails(
            $details['ean13'],
            $details['isbn'],
            $details['mpn'],
            $details['reference'],
            $details['upc'],
            new DecimalNumber($details['impact on weight'] ?? '0')
        );
    }

    /**
     * @Then combination :combinationReference should have following prices:
     *
     * @param string $combinationReference
     * @param CombinationPrices $expectedPrices
     */
    public function assertCombinationPrices(string $combinationReference, CombinationPrices $expectedPrices): void
    {
        $actualPrices = $this->getCombinationForEditing($combinationReference, $this->getDefaultShopId())->getPrices();

        Assert::assertTrue(
            $expectedPrices->getImpactOnPrice()->equals($actualPrices->getImpactOnPrice()),
            sprintf(
                'Unexpected combination impact on price. Expected "%s", got "%s"',
                (string) $expectedPrices->getImpactOnPrice(),
                (string) $actualPrices->getImpactOnPrice()
            )
        );
        Assert::assertTrue(
            $expectedPrices->getImpactOnPriceTaxIncluded()->equals($actualPrices->getImpactOnPriceTaxIncluded()),
            sprintf(
                'Unexpected combination impact on price with taxes. Expected "%s", got "%s"',
                (string) $expectedPrices->getImpactOnPriceTaxIncluded(),
                (string) $actualPrices->getImpactOnPriceTaxIncluded()
            )
        );

        Assert::assertTrue(
            $expectedPrices->getEcotax()->equals($actualPrices->getEcotax()),
            sprintf(
                'Unexpected combination eco tax. Expected "%s", got "%s"',
                (string) $expectedPrices->getEcotax(),
                (string) $actualPrices->getEcotax()
            )
        );
        Assert::assertTrue(
            $expectedPrices->getEcotaxTaxIncluded()->equals($actualPrices->getEcotaxTaxIncluded()),
            sprintf(
                'Unexpected combination eco tax with taxes. Expected "%s", got "%s"',
                (string) $expectedPrices->getEcotaxTaxIncluded(),
                (string) $actualPrices->getEcotaxTaxIncluded()
            )
        );

        Assert::assertTrue(
            $expectedPrices->getImpactOnUnitPrice()->equals($actualPrices->getImpactOnUnitPrice()),
            sprintf(
                'Unexpected combination impact on unit price. Expected "%s", got "%s"',
                (string) $expectedPrices->getImpactOnUnitPrice(),
                (string) $actualPrices->getImpactOnUnitPrice()
            )
        );
        Assert::assertTrue(
            $expectedPrices->getImpactOnPriceTaxIncluded()->equals($actualPrices->getImpactOnPriceTaxIncluded()),
            sprintf(
                'Unexpected combination impact on unit price with taxes. Expected "%s", got "%s"',
                (string) $expectedPrices->getImpactOnPriceTaxIncluded(),
                (string) $actualPrices->getImpactOnPriceTaxIncluded()
            )
        );

        Assert::assertTrue(
            $expectedPrices->getWholesalePrice()->equals($actualPrices->getWholesalePrice()),
            sprintf(
                'Unexpected combination wholesale price. Expected "%s", got "%s"',
                (string) $expectedPrices->getWholesalePrice(),
                (string) $actualPrices->getWholesalePrice()
            )
        );

        Assert::assertTrue(
            $expectedPrices->getProductTaxRate()->equals($actualPrices->getProductTaxRate()),
            sprintf(
                'Unexpected combination product tax rate. Expected "%s", got "%s"',
                (string) $expectedPrices->getProductTaxRate(),
                (string) $actualPrices->getProductTaxRate()
            )
        );

        Assert::assertTrue(
            $expectedPrices->getProductPrice()->equals($actualPrices->getProductPrice()),
            sprintf(
                'Unexpected combination product price. Expected "%s", got "%s"',
                (string) $expectedPrices->getProductPrice(),
                (string) $actualPrices->getProductPrice()
            )
        );

        Assert::assertTrue(
            $expectedPrices->getProductEcotax()->equals($actualPrices->getProductEcotax()),
            sprintf(
                'Unexpected combination wholesale price. Expected "%s", got "%s"',
                (string) $expectedPrices->getProductEcotax(),
                (string) $actualPrices->getProductEcotax()
            )
        );
    }

    /**
     * @Transform table:combination price detail,value
     *
     * @param TableNode $tableNode
     *
     * @return CombinationPrices
     */
    public function transformCombinationPrices(TableNode $tableNode): CombinationPrices
    {
        $dataRows = $tableNode->getRowsHash();

        return new CombinationPrices(
            new DecimalNumber($dataRows['impact on price']),
            new DecimalNumber($dataRows['impact on price with taxes']),
            new DecimalNUmber($dataRows['impact on unit price']),
            new DecimalNUmber($dataRows['impact on unit price with taxes']),
            new DecimalNumber($dataRows['eco tax']),
            new DecimalNumber($dataRows['eco tax with taxes']),
            new DecimalNUmber($dataRows['wholesale price']),
            new DecimalNUmber($dataRows['product tax rate']),
            new DecimalNUmber($dataRows['product price']),
            new DecimalNUmber($dataRows['product ecotax'])
        );
    }

    /**
     * @Then combination :combinationReference should have :availableQuantity available items
     *
     * @param string $combinationReference
     * @param int $availableQuantity
     */
    public function assertCombinationAvailableQuantity(string $combinationReference, int $availableQuantity): void
    {
        $actualStock = $this->getCombinationForEditing($combinationReference, $this->getDefaultShopId())->getStock();
        Assert::assertSame(
            $availableQuantity,
            $actualStock->getQuantity(),
            sprintf('Unexpected combination "%s" quantity', $combinationReference)
        );
    }

    /**
     * @Then I should get error that it is not allowed to perform update using both - delta and fixed quantity
     *
     * @return void
     */
    public function assertLastErrorIsDuplicateQuantityUpdate(): void
    {
        $this->assertLastErrorIs(
            ProductStockConstraintException::class,
            ProductStockConstraintException::FIXED_AND_DELTA_QUANTITY_PROVIDED
        );
    }

    /**
     * @Then combination :combinationReference should have following stock details:
     *
     * @param string $combinationReference
     * @param CombinationStock $expectedStock
     */
    public function assertStockDetails(string $combinationReference, CombinationStock $expectedStock): void
    {
        $actualStock = $this->getCombinationForEditing($combinationReference, $this->getDefaultShopId())->getStock();

        Assert::assertSame(
            $expectedStock->getQuantity(),
            $actualStock->getQuantity(),
            sprintf('Unexpected combination "%s" quantity', $combinationReference)
        );
        Assert::assertSame(
            $expectedStock->getMinimalQuantity(),
            $actualStock->getMinimalQuantity(),
            sprintf('Unexpected combination "%s" minimal quantity', $combinationReference)
        );
        Assert::assertSame(
            $expectedStock->getLowStockThreshold(),
            $actualStock->getLowStockThreshold(),
            sprintf('Unexpected combination "%s" low stock threshold', $combinationReference)
        );
        Assert::assertSame(
            $expectedStock->isLowStockAlertEnabled(),
            $actualStock->isLowStockAlertEnabled(),
            sprintf('Unexpected combination "%s" low stock alert', $combinationReference)
        );
        Assert::assertSame(
            $expectedStock->getLocation(),
            $actualStock->getLocation(),
            sprintf('Unexpected combination "%s" location', $combinationReference)
        );
        if (null === $expectedStock->getAvailableDate()) {
            Assert::assertSame(
                $expectedStock->getAvailableDate(),
                $actualStock->getAvailableDate(),
                sprintf('Unexpected combination "%s" availability date. Expected NULL, got "%s"',
                    $combinationReference,
                    var_export($actualStock->getAvailableDate(), true)
                )
            );
        } else {
            Assert::assertEquals(
                $expectedStock->getAvailableDate()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                $actualStock->getAvailableDate()->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                sprintf('Unexpected combination "%s" availability date', $combinationReference)
            );
        }
        $this->assertLocalizedProperty(
            $expectedStock->getLocalizedAvailableNowLabels(),
            $actualStock->getLocalizedAvailableNowLabels(),
            'available now label'
        );
        $this->assertLocalizedProperty(
            $expectedStock->getLocalizedAvailableLaterLabels(),
            $actualStock->getLocalizedAvailableLaterLabels(),
            'available later label'
        );
    }

    /**
     * @Transform table:combination stock detail,value
     *
     * @param TableNode $tableNode
     *
     * @return CombinationStock
     */
    public function transformCombinationStock(TableNode $tableNode): CombinationStock
    {
        $dataRows = $tableNode->getRowsHash();

        return new CombinationStock(
            (int) $dataRows['quantity'],
            (int) $dataRows['minimal quantity'],
            (int) $dataRows['low stock threshold'],
            PrimitiveUtils::castStringBooleanIntoBoolean($dataRows['low stock alert is enabled']),
            $dataRows['location'],
            '' === $dataRows['available date'] ? null : new DateTime($dataRows['available date']),
            !empty($dataRows['available now labels']) ? $dataRows['available now labels'] : [],
            !empty($dataRows['available later labels']) ? $dataRows['available later labels'] : []
        );
    }

    /**
     * @Then /^all combinations of product "([^"]*)" should have the stock policy to "([^"]*)"$/
     */
    public function allCombinationsOfProductShouldHaveTheStockPolicyTo(string $reference, string $outOfStock)
    {
        $product = $this->getProductForEditing($reference);

        $outOfStockInt = $this->convertOutOfStockToInt($outOfStock);
        Assert::assertSame(
            $product->getStockInformation()->getOutOfStockType(),
            $outOfStockInt
        );

        $combinations = $this->getCombinationsList($reference, $this->getDefaultShopId());

        foreach ($combinations->getCombinations() as $combination) {
            $id = StockAvailable::getStockAvailableIdByProductId(
                $this->getSharedStorage()->get($reference),
                $combination->getCombinationId()
            );

            Assert::assertSame(
                (int) (new StockAvailable($id))->out_of_stock,
                $outOfStockInt
            );
        }
    }

    private function assertLocalizedProperty(array $expectedValues, array $actualValues, string $fieldName): void
    {
        foreach ($expectedValues as $langId => $expectedValue) {
            $langIso = Language::getIsoById($langId);

            if (!isset($actualValues[$langId])) {
                throw new RuntimeException(sprintf(
                    'Expected localized %s value is not set in %s language',
                    $fieldName,
                    $langIso
                ));
            }

            if ($expectedValue !== $actualValues[$langId]) {
                throw new RuntimeException(
                    sprintf(
                        'Expected %s in "%s" language was "%s", but got "%s"',
                        $fieldName,
                        $langIso,
                        var_export($expectedValue, true),
                        var_export($actualValues[$langId], true)
                    )
                );
            }
        }
    }

    public function assertDetails(string $combinationReference, CombinationDetails $expectedDetails, array $shopIds): void
    {
        foreach ($shopIds as $shopId) {
            $scalarDetailNames = ['ean13', 'isbn', 'mpn', 'reference', 'upc'];
            $actualDetails = $this->getCombinationForEditing($combinationReference, $shopId)->getDetails();
            $propertyAccessor = PropertyAccess::createPropertyAccessor();

            foreach ($scalarDetailNames as $propertyName) {
                Assert::assertSame(
                    $propertyAccessor->getValue($expectedDetails, $propertyName),
                    $propertyAccessor->getValue($actualDetails, $propertyName),
                    sprintf('Unexpected %s of "%s for shop %d"', $propertyName, $combinationReference, $shopId)
                );
            }

            Assert::assertTrue(
                $expectedDetails->getImpactOnWeight()->equals($actualDetails->getImpactOnWeight()),
                sprintf(
                    'Unexpected combination impact on weight for shop %d. Expected "%s" got "%s"',
                    var_export($expectedDetails->getImpactOnWeight(), true),
                    var_export($actualDetails->getImpactOnWeight(), true),
                    $shopId
                )
            );
        }
    }
}
