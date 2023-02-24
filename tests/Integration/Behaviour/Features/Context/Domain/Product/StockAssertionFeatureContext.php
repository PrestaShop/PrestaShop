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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetProductStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;

/**
 * Context for assertions related to product Stock
 */
class StockAssertionFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @Then I should get error that product stock location is invalid
     */
    public function assertLastErrorIsInvalidStockLocation(): void
    {
        $this->assertLastErrorIs(
            ProductStockConstraintException::class,
            ProductStockConstraintException::INVALID_LOCATION
        );
    }

    /**
     * @Then product :productReference should have following stock information:
     */
    public function assertStockInformationForDefaultShop(string $productReference, TableNode $table): void
    {
        $this->assertStockInformation(
            $productReference,
            $table,
            $this->getDefaultShopId()
        );
    }

    /**
     * @Then product :productReference should have following stock information for shop(s) :shopReferences:
     */
    public function assertStockInformationForShops(
        string $productReference,
        string $shopReferences,
        TableNode $table
    ): void {
        $shopReferences = explode(',', $shopReferences);

        foreach ($shopReferences as $shopReference) {
            $this->assertStockInformation(
                $productReference,
                $table,
                $this->getSharedStorage()->get(trim($shopReference))
            );
        }
    }

    /**
     * @Then product :productReference should have no stock movements
     */
    public function assertNoStockMovementForDefaultShop(string $productReference): void
    {
        $this->assertNoStockMovementForProduct(
            $productReference,
            $this->getDefaultShopId()
        );
    }

    /**
     * @Then product :productReference should have no stock movements for shop(s) :shopReferences
     */
    public function assertNoStockMovementForSpecificShop(string $productReference, string $shopReferences): void
    {
        foreach ($this->getShopIdsFromReferences($shopReferences) as $shopId) {
            $this->assertNoStockMovementForProduct(
                $productReference,
                $shopId
            );
        }
    }

    /**
     * @Then product :productReference last stock movements should be:
     */
    public function assertProductLastStockMovementsForDefaultShop(string $productReference, TableNode $table): void
    {
        $this->assertLastStockMovementsForProduct(
            $productReference,
            $this->getDefaultShopId(),
            $table
        );
    }

    /**
     * @Then product :productReference last stock movements for shop(s) :shopReference should be:
     */
    public function assertProductLastStockMovementsForSpecificShop(
        string $productReference,
        string $shopReferences,
        TableNode $table
    ): void {
        foreach ($this->getShopIdsFromReferences($shopReferences) as $shopId) {
            $this->assertLastStockMovementsForProduct(
                $productReference,
                $shopId,
                $table
            );
        }
    }

    /**
     * @Then combination :combinationReference last stock movements should be:
     */
    public function assertCombinationLastStockMovementsForDefaultShop(string $combinationReference, TableNode $table): void
    {
        $this->assertLastStockMovementsForCombination(
            $combinationReference,
            $this->getDefaultShopId(),
            $table
        );
    }

    /**
     * @Then combination :combinationReference last stock movements for shop(s) :shopReferences should be:
     */
    public function assertCombinationLastStockMovementsForSpecificShop(
        string $combinationReference,
        string $shopReferences,
        TableNode $table
    ): void {
        foreach ($this->referencesToIds($shopReferences) as $shopId) {
            $this->assertLastStockMovementsForCombination(
                $combinationReference,
                $shopId,
                $table
            );
        }
    }

    /**
     * @Then combination ":combinationReference" should have no stock movements for shop ":shopReference"
     *
     * @param string $combinationReference
     * @param string $shopReference
     */
    public function assertCombinationNoLastStockMovementsForShop(
        string $combinationReference,
        string $shopReference
    ): void {
        $this->assertNoStockMovementForCombination($combinationReference, $this->getSharedStorage()->get($shopReference));
    }

    /**
     * @Then /^product "(.*)" last stock movement (increased|decreased) by (\d+)$/
     */
    public function assertProductLastStockMovementForDefaultShop(
        string $productReference,
        string $movementType,
        int $movementQuantity
    ): void {
        $this->assertProductLastStockMovement(
            $productReference,
            $movementType,
            $movementQuantity,
            $this->getDefaultShopId()
        );
    }

    /**
     * @Then /^product "(.*)" last stock movement for shop "(.*)" (increased|decreased) by (\d+)$/
     */
    public function assertProductLastStockMovementForSpecificShop(
        string $productReference,
        string $shopReference,
        string $movementType,
        int $movementQuantity
    ): void {
        $this->assertProductLastStockMovement(
            $productReference,
            $movementType,
            $movementQuantity,
            $this->getSharedStorage()->get(trim($shopReference))
        );
    }

    /**
     * @Then /^combination "(.*)" last stock movement (increased|decreased) by (\d+)$/
     */
    public function assertCombinationLastStockMovementForDefaultShop(
        string $combinationReference,
        string $movementType,
        int $movementQuantity
    ): void {
        $this->assertCombinationLastStockMovement(
            $combinationReference,
            $movementType,
            $movementQuantity,
            $this->getDefaultShopId()
        );
    }

    /**
     * @Then /^combination "(.*)" last stock movement for shop "(.*)" (increased|decreased) by (\d+)$/
     */
    public function assertCombinationLastStockMovementForSpecificShop(
        string $combinationReference,
        string $shopReference,
        string $movementType,
        int $movementQuantity
    ): void {
        $this->assertCombinationLastStockMovement(
            $combinationReference,
            $movementType,
            $movementQuantity,
            $this->getSharedStorage()->get(trim($shopReference))
        );
    }

    /**
     * @Then I should get error that pack stock type is invalid
     */
    public function assertInvalidPackStockType(): void
    {
        $this->assertLastErrorIs(
            ProductPackConstraintException::class,
            ProductPackConstraintException::INVALID_STOCK_TYPE
        );
    }

    /**
     * @Then I should get error that out of stock type is invalid
     */
    public function assertInvalidOutOfStockType(): void
    {
        $this->assertLastErrorIs(
            ProductStockConstraintException::class,
            ProductStockConstraintException::INVALID_OUT_OF_STOCK_TYPE
        );
    }

    /**
     * @param StockMovement[] $stockMovements
     * @param TableNode $table
     * @param int $shopId
     */
    private function assertStockMovementHistories(array $stockMovements, TableNode $table, int $shopId): void
    {
        $movementsData = $table->getColumnsHash();

        Assert::assertEquals(count($movementsData), count($stockMovements), sprintf(
            'Unexpected number of movements for shop %d expected %d but got %d instead',
            $shopId,
            count($movementsData),
            count($stockMovements)
        ));
        $index = 0;
        foreach ($movementsData as $movementDatum) {
            $stockMovement = $stockMovements[$index];
            Assert::assertEquals(
                $movementDatum['employee'],
                $stockMovement->getEmployeeName(),
                sprintf(
                    'Invalid employee name of stock movement event for shop %d, expected "%s" instead of "%s"',
                    $shopId,
                    $movementDatum['employee'],
                    $stockMovement->getEmployeeName()
                )
            );
            Assert::assertEquals(
                (int) $movementDatum['delta_quantity'],
                $stockMovement->getDeltaQuantity(),
                sprintf(
                    'Invalid delta quantity of stock movement event for shop %d, expected "%d" instead of "%d"',
                    $shopId,
                    $movementDatum['delta_quantity'],
                    $stockMovement->getDeltaQuantity()
                )
            );
            foreach ($this->resolveHistoryDateKeys($stockMovement->getType()) as $dateKey) {
                Assert::assertInstanceOf(
                    DateTimeImmutable::class,
                    $stockMovement->getDate($dateKey)
                );
            }

            ++$index;
        }
    }

    private function assertStockMovements(
        StockMovement $stockMovement,
        string $movementType,
        int $movementQuantity
    ): void {
        $lastMovementType = $stockMovement->getDeltaQuantity() < 0 ? 'decreased' : 'increased';

        Assert::assertEquals(
            $movementType,
            $lastMovementType,
            sprintf(
                'Invalid stock movement type, expected "%s" instead of "%s"',
                $movementType,
                $lastMovementType
            )
        );
        Assert::assertEquals(
            $movementQuantity,
            abs($stockMovement->getDeltaQuantity()),
            sprintf(
                'Invalid stock movement quantity, expected "%d" instead of "%d"',
                $movementQuantity,
                abs($stockMovement->getDeltaQuantity())
            )
        );
    }

    private function assertCombinationLastStockMovement(
        string $combinationReference,
        string $movementType,
        int $movementQuantity,
        int $shopId
    ): void {
        $combinationId = (int) $this->getSharedStorage()->get($combinationReference);
        $stockMovementHistories = $this->getQueryBus()->handle(
            new GetCombinationStockMovements($combinationId, $shopId)
        );
        $this->assertStockMovements(
            $stockMovementHistories[0],
            $movementType,
            $movementQuantity
        );
    }

    private function assertProductLastStockMovement(
        string $productReference,
        string $movementType,
        int $movementQuantity,
        int $shopId
    ): void {
        $productId = (int) $this->getSharedStorage()->get($productReference);
        $stockMovementHistories = $this->getQueryBus()->handle(
            new GetProductStockMovements($productId, $shopId)
        );
        $this->assertStockMovements(
            $stockMovementHistories[0],
            $movementType,
            $movementQuantity
        );
    }

    private function assertLastStockMovementsForCombination(
        string $combinationReference,
        int $shopId,
        TableNode $table
    ): void {
        $combinationId = (int) $this->getSharedStorage()->get($combinationReference);
        $stockMovementHistories = $this->getQueryBus()->handle(
            new GetCombinationStockMovements($combinationId, $shopId)
        );
        $this->assertStockMovementHistories($stockMovementHistories, $table, $shopId);
    }

    private function assertLastStockMovementsForProduct(
        string $productReference,
        int $shopId,
        TableNode $table
    ): void {
        $productId = (int) $this->getSharedStorage()->get($productReference);
        $stockMovementHistories = $this->getQueryBus()->handle(
            new GetProductStockMovements($productId, $shopId)
        );
        $this->assertStockMovementHistories($stockMovementHistories, $table, $shopId);
    }

    private function assertStockInformation(string $productReference, TableNode $table, int $shopId): void
    {
        $shopErrorMessage = !empty($shopId) ? sprintf(' for shop %s', $shopId) : '';
        $productForEditing = $this->getProductForEditing($productReference, $shopId);
        $data = $table->getRowsHash();

        if (isset($data['out_of_stock_type'])) {
            $data['out_of_stock_type'] = $this->convertOutOfStockToInt($data['out_of_stock_type']);
        }
        if (isset($data['pack_stock_type'])) {
            $data['pack_stock_type'] = $this->convertPackStockTypeToInt($data['pack_stock_type']);
        }

        $this->assertStringProperty($productForEditing, $data, 'pack_stock_type', $shopErrorMessage);
        $this->assertIntegerProperty($productForEditing, $data, 'out_of_stock_type', $shopErrorMessage);
        $this->assertIntegerProperty($productForEditing, $data, 'quantity', $shopErrorMessage);
        $this->assertIntegerProperty($productForEditing, $data, 'minimal_quantity', $shopErrorMessage);
        $this->assertStringProperty($productForEditing, $data, 'location', $shopErrorMessage);
        $this->assertIntegerProperty($productForEditing, $data, 'low_stock_threshold', $shopErrorMessage);
        $this->assertBoolProperty($productForEditing, $data, 'low_stock_alert', $shopErrorMessage);
        $this->assertDateTimeProperty($productForEditing, $data, 'available_date', $shopErrorMessage);

        // Assertions checking isset() can hide some errors if it doesn't find array key,
        // to make sure all provided fields were checked we need to unset every asserted field
        // and finally, if provided data is not empty, it means there are some unnasserted values left
        Assert::assertEmpty(
            $data,
            sprintf('Some provided product stock fields haven\'t been asserted: %s', var_export($data, true))
        );
    }

    private function assertNoStockMovementForProduct(string $productReference, int $shopId): void
    {
        $productId = $this->getSharedStorage()->get($productReference);

        $stockMovementHistories = $this->getQueryBus()->handle(
            new GetProductStockMovements($productId, $shopId)
        );
        Assert::assertEmpty($stockMovementHistories, 'Expected to find no stock movements');
    }

    private function assertNoStockMovementForCombination(string $combinationReference, int $shopId): void
    {
        $combinationId = $this->getSharedStorage()->get($combinationReference);

        $stockMovementHistories = $this->getQueryBus()->handle(
            new GetCombinationStockMovements($combinationId, $shopId)
        );
        Assert::assertEmpty($stockMovementHistories, 'Expected to find no stock movements');
    }
}
