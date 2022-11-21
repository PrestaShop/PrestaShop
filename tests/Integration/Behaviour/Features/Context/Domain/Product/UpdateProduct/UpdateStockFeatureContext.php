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

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product\UpdateProduct;

use Behat\Gherkin\Node\TableNode;
use Cache;
use DateTime;
use Pack;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\ProductStockConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

class UpdateStockFeatureContext extends AbstractStockFeatureContext
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

    /**
     * @When I update product :productReference location with value of :length symbols length
     */
    public function updateLocationWithTooLongName(string $productReference, int $length): void
    {
        $command = new UpdateProductStockCommand(
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
     * @param string $productReference
     * @param TableNode $table
     * @param ShopConstraint $shopConstraint
     */
    private function updateProductStock(
        string $productReference,
        TableNode $table,
        ShopConstraint $shopConstraint
    ): void {
        $data = $this->localizeByRows($table);
        $productId = $this->getSharedStorage()->get($productReference);

        try {
            $updateStockCommand = new UpdateProductStockCommand($productId, $shopConstraint);
            $this->setUpdateStockCommandData($data, $updateStockCommand);

            $updateProductCommand = new UpdateProductCommand($productId, $shopConstraint);
            $this->setUpdateProductCommandData($data, $updateProductCommand);

            $this->getCommandBus()->handle($updateStockCommand);
            $this->getCommandBus()->handle($updateProductCommand);

            // Clean the cache or legacy code won't return the right quantity in following steps
            Cache::clean('StockAvailable::*');
        } catch (ProductException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param UpdateProductCommand $command
     */
    private function setUpdateProductCommandData(array $data, UpdateProductCommand $command): void
    {
        if (isset($data['pack_stock_type'])) {
            // If pack is involved we clear the cache because its products settings might have changed
            Pack::resetStaticCache();
            $command->setPackStockType($this->convertPackStockTypeToInt($data['pack_stock_type']));
        }

        if (isset($data['minimal_quantity'])) {
            $command->setMinimalQuantity((int) $data['minimal_quantity']);
        }

        if (isset($data['low_stock_threshold'])) {
            $command->setLowStockThreshold((int) $data['low_stock_threshold']);
        }

        if (isset($data['low_stock_alert'])) {
            $command->setLowStockAlert(PrimitiveUtils::castStringBooleanIntoBoolean($data['low_stock_alert']));
        }

        if (isset($data['available_now_labels'])) {
            $command->setLocalizedAvailableNowLabels($data['available_now_labels']);
        }

        if (isset($data['available_later_labels'])) {
            $command->setLocalizedAvailableLaterLabels($data['available_later_labels']);
        }

        if (isset($data['available_date'])) {
            $command->setAvailableDate(new DateTime($data['available_date']));
        }
    }

    /**
     * @param array<string, mixed> $data
     * @param UpdateProductStockCommand $command
     */
    private function setUpdateStockCommandData(array $data, UpdateProductStockCommand $command): void
    {
        if (isset($data['out_of_stock_type'])) {
            $command->setOutOfStockType($this->convertOutOfStockToInt($data['out_of_stock_type']));
        }

        if (isset($data['delta_quantity'])) {
            $command->setDeltaQuantity((int) $data['delta_quantity']);
        }

        if (isset($data['location'])) {
            $command->setLocation($data['location']);
        }
    }
}
