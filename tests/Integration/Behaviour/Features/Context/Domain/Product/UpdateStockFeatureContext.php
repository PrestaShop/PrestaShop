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
use DateTime;
use Pack;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\Exception\ProductPackConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\ValueObject\PackStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockInformationCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Query\GetEmployeesStockMovements;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\EmployeeStockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\QueryResult\StockMovement;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\OutOfStockType;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Api\QueryStockMovementParamsCollection;
use PrestaShopBundle\Entity\Repository\StockMovementRepository;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateStockFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I update product :productReference stock with following information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductStockForDefaultShop(string $productReference, TableNode $table): void
    {
        $this->updateProductStock($productReference, $table, ShopConstraint::shop($this->getDefaultShopId()));
    }

    /**
     * @When I update product :productReference stock for shop :shopReference with following information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function updateProductStockForShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $shopConstraint = ShopConstraint::shop($shopId);
        $this->updateProductStock($productReference, $table, $shopConstraint);
    }

    /**
     * @When I update product :productReference stock for all shops with following information:
     *
     * @param TableNode $table
     */
    public function updateProductStockForAllShops(string $productReference, TableNode $table): void
    {
        $this->updateProductStock($productReference, $table, ShopConstraint::allShops());
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     */
    private function updateProductStock(string $productReference, TableNode $table, ShopConstraint $shopConstraint): void
    {
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
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I update product :productReference location with value of :length symbols length
     *
     * @param string $productReference
     * @param int $length
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
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertStockInformationForDefaultShop(string $productReference, TableNode $table): void
    {
        $this->assertStockInformation($productReference, $table, $this->getDefaultShopId());
    }

    /**
     * @Then product :productReference should have following stock information for shops :shopReferences:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertStockInformationForShops(string $productReference, string $shopReferences, TableNode $table): void
    {
        $shopReferences = explode(',', $shopReferences);
        foreach ($shopReferences as $shopReference) {
            $shopId = $this->getSharedStorage()->get(trim($shopReference));
            $this->assertStockInformation($productReference, $table, $shopId);
        }
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     */
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
        Assert::assertEmpty($data, sprintf('Some provided product stock fields haven\'t been asserted: %s', var_export($data, true)));
    }

    /**
     * @Then product :productReference should have no stock movements
     *
     * @param string $productReference
     */
    public function assertNoEmployeesStockMovementForDefaultShop(string $productReference): void
    {
        $this->assertNoEmployeesStockMovement($productReference, $this->getDefaultShopId());
    }

    /**
     * @Then product :productReference should have no stock movements for shop :shopReference
     *
     * @param string $productReference
     * @param string $shopReference
     */
    public function assertNoEmployeesStockMovementForSpecificShop(string $productReference, string $shopReference): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $this->assertNoEmployeesStockMovement($productReference, $shopId);
    }

    /**
     * @param string $productReference
     * @param int $shopId
     */
    private function assertNoEmployeesStockMovement(string $productReference, int $shopId): void
    {
        $productId = (int) $this->getSharedStorage()->get($productReference);

        /** @var StockMovement[] $stockMovements */
        $stockMovements = $this->getQueryBus()->handle(new GetEmployeesStockMovements(
            $productId,
            $shopId
        ));
        Assert::assertEmpty($stockMovements, 'Expected to find no stock movements');
    }

    /**
     * @Then product :productReference last employees stock movements should be:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertLastEmployeesStockMovementForProductAndDefaultShop(string $productReference, TableNode $table): void
    {
        $this->assertLastEmployeesStockMovementForProduct($productReference, $table, $this->getDefaultShopId());
    }

    /**
     * @Then product :productReference last employees stock movements for shop :shopReference should be:
     *
     * @param string $productReference
     * @param string $shopReference
     * @param TableNode $table
     */
    public function assertLastEmployeesStockMovementForProductAndSpecificShop(string $productReference, string $shopReference, TableNode $table): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $this->assertLastEmployeesStockMovementForProduct($productReference, $table, $shopId);
    }

    /**
     * @param string $productReference
     * @param TableNode $table
     */
    private function assertLastEmployeesStockMovementForProduct(string $productReference, TableNode $table, int $shopId): void
    {
        $productId = (int) $this->getSharedStorage()->get($productReference);

        /** @var EmployeeStockMovement[] $stockMovements */
        $stockMovements = $this->getQueryBus()->handle(new GetEmployeesStockMovements(
            $productId,
            $shopId
        ));
        $this->assertStockMovements($stockMovements, $table);
    }

    /**
     * @Then combination :combinationReference last employees stock movements should be:
     *
     * @param string $combinationReference
     * @param TableNode $table
     */
    public function assertLastEmployeesStockMovementForCombination(string $combinationReference, TableNode $table): void
    {
        $stockMovements = $this->getLastEmployeesStockMovementsForCombination($combinationReference);

        $this->assertStockMovements($stockMovements, $table);
    }

    /**
     * @Then /^product "(.*)" last stock movement (increased|decreased) by (\d+)$/
     *
     * @param string $productReference
     * @param string $movementType
     * @param int $movementQuantity
     */
    public function assertProductLastStockMovementForDefaultShop(string $productReference, string $movementType, int $movementQuantity): void
    {
        $this->assertProductLastStockMovement($productReference, $movementType, $movementQuantity, $this->getDefaultShopId());
    }

    /**
     * @Then /^product "(.*)" last stock movement for shop "(.*)" (increased|decreased) by (\d+)$/
     *
     * @param string $productReference
     * @param string $movementType
     * @param int $movementQuantity
     */
    public function assertProductLastStockMovementForSpecificShop(string $productReference, string $shopReference, string $movementType, int $movementQuantity): void
    {
        $shopId = $this->getSharedStorage()->get(trim($shopReference));
        $this->assertProductLastStockMovement($productReference, $movementType, $movementQuantity, $shopId);
    }

    /**
     * @param string $productReference
     * @param string $movementType
     * @param int $movementQuantity
     * @param int $shopId
     */
    private function assertProductLastStockMovement(string $productReference, string $movementType, int $movementQuantity, int $shopId): void
    {
        $productId = (int) $this->getSharedStorage()->get($productReference);

        /** @var EmployeeStockMovement[] $stockMovements */
        $stockMovements = $this->getQueryBus()->handle(new GetEmployeesStockMovements(
            $productId,
            $shopId
        ));
        $this->assertLastStockMovement($stockMovements[0], $movementType, $movementQuantity);
    }

    /**
     * @Then /^combination "(.*)" last stock movement (increased|decreased) by (\d+)$/
     *
     * @param string $combinationReference
     * @param string $movementType
     * @param int $movementQuantity
     */
    public function assertCombinationLastStockMovement(string $combinationReference, string $movementType, int $movementQuantity): void
    {
        $stockMovements = $this->getLastEmployeesStockMovementsForCombination($combinationReference);
        $this->assertLastStockMovement($stockMovements[0], $movementType, $movementQuantity);
    }

    /**
     * @Then product :productReference has no stock movements
     *
     * @param string $productReference
     */
    public function assertProductHasNoStockMovement(string $productReference): void
    {
        $productId = $this->getSharedStorage()->get($productReference);

        /** @var StockMovementRepository $stockMovementRepository */
        $stockMovementRepository = $this->getContainer()->get('prestashop.core.api.stock_movement.repository');
        $params = new QueryStockMovementParamsCollection();
        $params->fromArray([
            'productId' => $productId,
        ]);
        $movements = $stockMovementRepository->getData($params);
        if (count($movements) > 0) {
            throw new RuntimeException(sprintf('Unexpected stock movement found for product %s', $productReference));
        }
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
     * @param array $data
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

    /**
     * @param string $outOfStock
     *
     * @return int
     */
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

    /**
     * @param string $outOfStock
     *
     * @return int
     */
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
     * @param EmployeeStockMovement[] $stockMovements
     * @param TableNode $table
     */
    private function assertStockMovements(array $stockMovements, TableNode $table): void
    {
        $movementsData = $table->getColumnsHash();

        Assert::assertEquals(count($movementsData), count($stockMovements));
        $index = 0;
        foreach ($movementsData as $movementDatum) {
            $stockMovement = $stockMovements[$index];
            Assert::assertEquals(
                $movementDatum['first_name'],
                $stockMovement->getFirstName(),
                sprintf(
                    'Invalid stock movement first name, expected %s instead of %s',
                    $movementDatum['first_name'],
                    $stockMovement->getFirstName()
                )
            );
            Assert::assertEquals(
                $movementDatum['last_name'],
                $stockMovement->getLastName(),
                sprintf(
                    'Invalid stock movement last name, expected %s instead of %s',
                    $movementDatum['last_name'],
                    $stockMovement->getLastName()
                )
            );
            Assert::assertEquals(
                (int) $movementDatum['delta_quantity'],
                $stockMovement->getDeltaQuantity(),
                sprintf(
                    'Invalid stock movement delta quantity, expected %d instead of %d',
                    $movementDatum['delta_quantity'],
                    $stockMovement->getDeltaQuantity()
                )
            );
            Assert::assertNotNull($stockMovement->getDateAdd());
            Assert::assertInstanceOf(DateTime::class, $stockMovement->getDateAdd());

            ++$index;
        }
    }

    /**
     * @param StockMovement $stockMovement
     * @param string $movementType
     * @param int $movementQuantity
     */
    private function assertLastStockMovement(StockMovement $stockMovement, string $movementType, int $movementQuantity): void
    {
        $lastMovementType = $stockMovement->getDeltaQuantity() < 0 ? 'decreased' : 'increased';
        Assert::assertEquals(
            $movementType,
            $lastMovementType,
            sprintf(
                'Invalid stock movement type, expected %s instead of %s',
                $movementType,
                $lastMovementType
            )
        );

        Assert::assertEquals(
            $movementQuantity,
            abs($stockMovement->getDeltaQuantity()),
            sprintf(
                'Invalid stock movement quantity, expected %d instead of %d',
                $movementQuantity,
                abs($stockMovement->getDeltaQuantity())
            )
        );
    }

    /**
     * @todo: refactor this method when query for combination stock movements is introduced
     *
     * @param string $combinationReference
     *
     * @return EmployeeStockMovement[]
     */
    private function getLastEmployeesStockMovementsForCombination(string $combinationReference): array
    {
        $combinationId = (int) $this->getSharedStorage()->get($combinationReference);
        $stockMovementRepository = $this->getContainer()->get('prestashop.adapter.product.stock.repository.stock_movement_repository');
        $stockAvailableRepository = $this->getContainer()->get('prestashop.adapter.product.stock.repository.stock_available_repository');
        $stockAvailable = $stockAvailableRepository->getForCombination(new CombinationId($combinationId));
        $stockId = new StockId($stockAvailable->id);

        $movementsData = $stockMovementRepository->getLastEmployeeStockMovements($stockId);

        $movements = [];
        foreach ($movementsData as $movementDatum) {
            $movements[] = new EmployeeStockMovement(
                (int) $movementDatum['id_stock_mvt'],
                (int) $movementDatum['id_stock'],
                (int) $movementDatum['id_stock_mvt_reason'],
                (int) $movementDatum['physical_quantity'] * (int) $movementDatum['sign'],
                (int) $movementDatum['id_employee'],
                $movementDatum['employee_firstname'],
                $movementDatum['employee_lastname'],
                new DateTime($movementDatum['date_add'])
            );
        }

        return $movements;
    }
}
