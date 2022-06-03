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
use Cache;
use DateTime;
use DateTimeImmutable;
use Pack;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetCombinationStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetProductStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovementEvent;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateStockFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference stock with following information:
     */
    public function updateProductStockForDefaultShop(string $productReference, TableNode $table): void
    {
        $this->updateProductStock(
            $productReference,
            $table,
            ShopConstraint::shop($this->getDefaultShopId())
        );
    }

    /**
     * @When I update product :productReference stock for shop :shopReference with following information:
     */
    public function updateProductStockForShop(
        string $productReference,
        string $shopReference,
        TableNode $table
    ): void {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $shopConstraint = ShopConstraint::shop($shopId);

        $this->updateProductStock(
            $productReference,
            $table,
            $shopConstraint
        );
    }

    /**
     * @When I update product :productReference stock for all shops with following information:
     */
    public function updateProductStockForAllShops(string $productReference, TableNode $table): void
    {
        $this->updateProductStock(
            $productReference,
            $table,
            ShopConstraint::allShops()
        );
    }

    private function updateProductStock(
        string $productReference,
        TableNode $table,
        ShopConstraint $shopConstraint
    ): void {
        $data = $this->localizeByRows($table);
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductStockInformationCommand($productId, $shopConstraint);
            $unhandledData = $this->setUpdateStockCommandData($data, $command);
            Assert::assertEmpty(
                $unhandledData,
                sprintf('Not all provided data was handled in scenario. Unhandled: %s', var_export($unhandledData, true))
            );
            $this->getCommandBus()->handle($command);

            // Clean the cache or legacy code won't return the right quantity in following steps
            Cache::clean('StockAvailable::*');
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update product :productReference location with value of :length symbols length
     */
    public function updateLocationWithTooLongName(string $productReference, int $length): void
    {
        $command = new UpdateProductStockInformationCommand(
            $this->getSharedStorage()->get($productReference),
            ShopConstraint::shop($this->getDefaultShopId())
        );
        $command->setLocation(PrimitiveUtils::generateRandomString($length));

        try {
            $this->getCommandBus()->handle($command);
        } catch (ProductStockConstraintException $e) {
            $this->setLastException($e);
        }
    }

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
     * @Then product :productReference should have following stock information for shops :shopReferences:
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
     * @Then product :productReference should have no stock movements for shop :shopReference
     */
    public function assertNoStockMovementForSpecificShop(string $productReference, string $shopReference): void
    {
        $this->assertNoStockMovementForProduct(
            $productReference,
            $this->getSharedStorage()->get(trim($shopReference))
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
     * @Then product :productReference last stock movements for shop :shopReference should be:
     */
    public function assertProductLastStockMovementsForSpecificShop(
        string $productReference,
        string $shopReference,
        TableNode $table
    ): void {
        $this->assertLastStockMovementsForProduct(
            $productReference,
            $this->getSharedStorage()->get(trim($shopReference)),
            $table
        );
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
        $this->assertStockMovementHistories($stockMovementHistories, $table);
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
     * @Then combination :combinationReference last stock movements for shop :shopReference should be:
     */
    public function assertCombinationLastStockMovementsForSpecificShop(
        string $combinationReference,
        string $shopReference,
        TableNode $table
    ): void {
        $this->assertLastStockMovementsForCombination(
            $combinationReference,
            $this->getSharedStorage()->get(trim($shopReference)),
            $table
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
        $this->assertStockMovementHistories($stockMovementHistories, $table);
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
     * @param array<string, mixed> $data
     * @param UpdateProductStockInformationCommand $command
     */
    private function setUpdateStockCommandData(array $data, UpdateProductStockInformationCommand $command): array
    {
        if (isset($data['pack_stock_type'])) {
            // If pack is involved we clear the cache because its products settings might have changed
            Pack::resetStaticCache();
            $command->setPackStockType($this->convertPackStockTypeToInt($data['pack_stock_type']));
            unset($data['pack_stock_type']);
        }

        if (isset($data['out_of_stock_type'])) {
            $command->setOutOfStockType($this->convertOutOfStockToInt($data['out_of_stock_type']));
            unset($data['out_of_stock_type']);
        }

        if (isset($data['delta_quantity'])) {
            $command->setDeltaQuantity((int) $data['delta_quantity']);
            unset($data['delta_quantity']);
        }

        if (isset($data['minimal_quantity'])) {
            $command->setMinimalQuantity((int) $data['minimal_quantity']);
            unset($data['minimal_quantity']);
        }

        if (isset($data['location'])) {
            $command->setLocation($data['location']);
            unset($data['location']);
        }

        if (isset($data['low_stock_threshold'])) {
            $command->setLowStockThreshold((int) $data['low_stock_threshold']);
            unset($data['low_stock_threshold']);
        }

        if (isset($data['low_stock_alert'])) {
            $command->setLowStockAlert(PrimitiveUtils::castStringBooleanIntoBoolean($data['low_stock_alert']));
            unset($data['low_stock_alert']);
        }

        if (isset($data['available_now_labels'])) {
            $command->setLocalizedAvailableNowLabels($data['available_now_labels']);
            unset($data['available_now_labels']);
        }

        if (isset($data['available_later_labels'])) {
            $command->setLocalizedAvailableLaterLabels($data['available_later_labels']);
            unset($data['available_later_labels']);
        }

        if (isset($data['available_date'])) {
            $command->setAvailableDate(new DateTime($data['available_date']));
            unset($data['available_date']);
        }

        return $data;
    }

    private function convertOutOfStockToInt(string $outOfStock): int
    {
        $intValues = [
            'default' => OutOfStockType::OUT_OF_STOCK_DEFAULT,
            'available' => OutOfStockType::OUT_OF_STOCK_AVAILABLE,
            'not_available' => OutOfStockType::OUT_OF_STOCK_NOT_AVAILABLE,
            'invalid' => 42, // This random number is hardcoded intentionally to reflect invalid stock type
        ];

        return $intValues[$outOfStock];
    }

    private function convertPackStockTypeToInt(string $outOfStock): int
    {
        $intValues = [
            'default' => PackStockType::STOCK_TYPE_DEFAULT,
            'products_only' => PackStockType::STOCK_TYPE_PRODUCTS_ONLY,
            'pack_only' => PackStockType::STOCK_TYPE_PACK_ONLY,
            'both' => PackStockType::STOCK_TYPE_BOTH,
            'invalid' => 42, // This random number is hardcoded intentionally to reflect invalid pack stock type
        ];

        return $intValues[$outOfStock];
    }

    /**
     * @param StockMovementEvent[] $stockMovementHistories
     * @param TableNode $table
     */
    private function assertStockMovementHistories(array $stockMovementHistories, TableNode $table): void
    {
        $movementsData = $table->getColumnsHash();

        Assert::assertEquals(count($movementsData), count($stockMovementHistories));
        $index = 0;
        foreach ($movementsData as $movementDatum) {
            $stockMovementEvent = $stockMovementHistories[$index];
            Assert::assertEquals(
                $movementDatum['employee'],
                $stockMovementEvent->getEmployeeName(),
                sprintf(
                    'Invalid employee name of stock movement event, expected "%s" instead of "%s"',
                    $movementDatum['employee'],
                    $stockMovementEvent->getEmployeeName()
                )
            );
            Assert::assertEquals(
                (int) $movementDatum['delta_quantity'],
                $stockMovementEvent->getDeltaQuantity(),
                sprintf(
                    'Invalid delta quantity of stock movement event, expected "%d" instead of "%d"',
                    $movementDatum['delta_quantity'],
                    $stockMovementEvent->getDeltaQuantity()
                )
            );
            foreach ($this->resolveHistoryDateKeys($stockMovementEvent->getType()) as $dateKey) {
                Assert::assertInstanceOf(
                    DateTimeImmutable::class,
                    $stockMovementEvent->getDate($dateKey)
                );
            }

            ++$index;
        }
    }

    private function assertStockMovements(
        StockMovementEvent $stockMovementEvent,
        string $movementType,
        int $movementQuantity
    ): void {
        $lastMovementType = $stockMovementEvent->getDeltaQuantity() < 0 ? 'decreased' : 'increased';

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
            abs($stockMovementEvent->getDeltaQuantity()),
            sprintf(
                'Invalid stock movement quantity, expected "%d" instead of "%d"',
                $movementQuantity,
                abs($stockMovementEvent->getDeltaQuantity())
            )
        );
    }
}
