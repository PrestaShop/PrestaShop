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
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
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
    public function updateProductStock(string $productReference, TableNode $table): void
    {
        $data = $table->getRowsHash();
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $command = new UpdateProductStockCommand($productId);
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
     * @Then product :productReference should have following stock information:
     *
     * @param string $productReference
     * @param TableNode $table
     */
    public function assertStockInformation(string $productReference, TableNode $table)
    {
        $productForEditing = $this->getProductForEditing($productReference);
        $data = $table->getRowsHash();

        $this->assertBoolProperty($productForEditing, $data, 'use_advanced_stock_management');
        $this->assertBoolProperty($productForEditing, $data, 'depends_on_stock');
        $this->assertStringProperty($productForEditing, $data, 'pack_stock_type');
        $this->assertStringProperty($productForEditing, $data, 'out_of_stock_type');
        $this->assertNumberProperty($productForEditing, $data, 'quantity');
        $this->assertNumberProperty($productForEditing, $data, 'minimal_quantity');
        $this->assertStringProperty($productForEditing, $data, 'location');
        $this->assertNumberProperty($productForEditing, $data, 'low_stock_threshold');
        $this->assertBoolProperty($productForEditing, $data, 'low_stock_alert');
        $this->assertDateProperty($productForEditing, $data, 'available_date');

        // Assertions checking isset() can hide some errors if it doesn't find array key,
        // to make sure all provided fields were checked we need to unset every asserted field
        // and finally, if provided data is not empty, it means there are some unnasserted values left
        Assert::assertEmpty($data, sprintf('Some provided product stock fields haven\'t been asserted: %s', var_export($data, true)));
    }

    /**
     * @Then product :productReference last stock movement has following details:
     */
    public function assertProductLastStockMovement(string $productReference, TableNode $table)
    {
        $movementData = $table->getRowsHash();
        $productId = $this->getSharedStorage()->get($productReference);

        /** @var StockMovementRepository $stockMovementRepository */
        $stockMovementRepository = $this->getContainer()->get('prestashop.core.api.stock_movement.repository');
        $params = new QueryStockMovementParamsCollection();
        $params->fromArray([
            'productId' => $productId,
        ]);
        $movements = $stockMovementRepository->getData($params);
        if (count($movements) <= 0) {
            throw new RuntimeException(sprintf('No stock movement found for product %s', $productReference));
        }

        $lastMovement = $movements[0];
        foreach ($movementData as $movementField => $movementValue) {
            Assert::assertEquals(
                $movementValue,
                $lastMovement[$movementField],
                sprintf(
                    'Invalid stock movement field %s, expected %s instead of %s',
                    $movementField,
                    $movementValue,
                    $lastMovement[$movementField]
                )
            );
        }
    }

    /**
     * @Then product :productReference has no stock movements
     */
    public function assertProductHasNoStockMovement(string $productReference)
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
     * @Then I should get error that stock management is disabled
     */
    public function assertStockManagementDisabledError(): void
    {
        $this->assertLastErrorIs(
            ProductStockConstraintException::class,
            ProductStockConstraintException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
        );
    }

    /**
     * @Then I should get error that stock management is disabled on product
     */
    public function assertStockManagementDisabledOnProductError(): void
    {
        $this->assertLastErrorIs(
            ProductStockConstraintException::class,
            ProductStockConstraintException::ADVANCED_STOCK_MANAGEMENT_PRODUCT_DISABLED
        );
    }

    /**
     * @Then I should get error that pack stock type is incompatible
     */
    public function assertIncompatiblePackStockTypeError(): void
    {
        $this->assertLastErrorIs(
            ProductStockConstraintException::class,
            ProductStockConstraintException::INCOMPATIBLE_PACK_STOCK_TYPE
        );
    }

    /**
     * @Then I should get error that out of stock type is invalid
     */
    public function assertInvalidOUtOfStockType(): void
    {
        $this->assertLastErrorIs(
            ProductStockConstraintException::class,
            ProductStockConstraintException::INVALID_OUT_OF_STOCK_TYPE
        );
    }

    /**
     * @param array $data
     * @param UpdateProductStockCommand $command
     */
    private function setUpdateStockCommandData(array $data, UpdateProductStockCommand $command): array
    {
        if (isset($data['use_advanced_stock_management'])) {
            $command->setUseAdvancedStockManagement(PrimitiveUtils::castStringBooleanIntoBoolean($data['use_advanced_stock_management']));
            unset($data['use_advanced_stock_management']);
        }

        if (isset($data['depends_on_stock'])) {
            $command->setDependsOnStock(PrimitiveUtils::castStringBooleanIntoBoolean($data['depends_on_stock']));
            unset($data['depends_on_stock']);
        }

        if (isset($data['pack_stock_type'])) {
            // If pack is involved we clear the cache because its products settings might have changed
            Pack::resetStaticCache();
            $command->setPackStockType($data['pack_stock_type']);
            unset($data['pack_stock_type']);
        }

        if (isset($data['out_of_stock_type'])) {
            $command->setOutOfStockType($data['out_of_stock_type']);
            unset($data['out_of_stock_type']);
        }

        if (isset($data['quantity'])) {
            $command->setQuantity((int) $data['quantity']);
            unset($data['quantity']);
        }

        if (isset($data['add_movement'])) {
            $command->setAddMovement(PrimitiveUtils::castStringBooleanIntoBoolean($data['add_movement']));
            unset($data['add_movement']);
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
            $command->setLocalizedAvailableNowLabels($this->parseLocalizedArray($data['available_now_labels']));
            unset($data['available_now_labels']);
        }

        if (isset($data['available_later_labels'])) {
            $command->setLocalizedAvailableLaterLabels($this->parseLocalizedArray($data['available_later_labels']));
            unset($data['available_later_labels']);
        }

        if (isset($data['available_date'])) {
            $command->setAvailableDate(new DateTime($data['available_date']));
            unset($data['available_date']);
        }

        return $data;
    }
}
