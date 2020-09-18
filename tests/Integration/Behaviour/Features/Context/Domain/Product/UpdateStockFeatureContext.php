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
use Pack;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductStockException;

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
            $this->setUpdateStockCommandData($data, $command);
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
    }

    /**
     * @Then I should get error that stock management is disabled
     */
    public function assertStockManagementDisabledError(): void
    {
        $this->assertLastErrorIs(
            ProductStockException::class,
            ProductStockException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
        );
    }

    /**
     * @Then I should get error that stock management is disabled on product
     */
    public function assertStockManagementDisabledOnProductError(): void
    {
        $this->assertLastErrorIs(
            ProductStockException::class,
            ProductStockException::ADVANCED_STOCK_MANAGEMENT_PRODUCT_DISABLED
        );
    }

    /**
     * @Then I should get error that pack stock type is incompatible
     */
    public function assertIncompatiblePackStockTypeError(): void
    {
        $this->assertLastErrorIs(
            ProductStockException::class,
            ProductStockException::INCOMPATIBLE_PACK_STOCK_TYPE
        );
    }

    /**
     * @param array $data
     * @param UpdateProductStockCommand $command
     */
    private function setUpdateStockCommandData(array $data, UpdateProductStockCommand $command): void
    {
        if (isset($data['use_advanced_stock_management'])) {
            $command->setUseAdvancedStockManagement((bool) $data['use_advanced_stock_management']);
        }

        if (isset($data['depends_on_stock'])) {
            $command->setDependsOnStock((bool) $data['depends_on_stock']);
        }

        if (isset($data['pack_stock_type'])) {
            // If pack is involved we clear the cache because its products settings might have changed
            Pack::resetStaticCache();
            $command->setPackStockType($data['pack_stock_type']);
        }
    }
}
