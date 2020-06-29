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

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use Pack;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Pack\PackSettings;
use PrestaShopException;
use PrestaShop\PrestaShop\Adapter\Product\AbstractProductHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductStockCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductStockCommandHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductStockException;
use Product;
use StockAvailable;

/**
 * @internal
 */
class UpdateProductStockCommandHandler extends AbstractProductHandler implements UpdateProductStockCommandHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var bool
     */
    private $synchronizationNeeded = false;

    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(UpdateProductStockCommand $command): void
    {
        $product = $this->getFullProduct($command->getProductId());
        $advancedStockEnabled = (bool) $this->configuration->get('PS_ADVANCED_STOCK_MANAGEMENT');
        if ($advancedStockEnabled) {
            $this->handleAdvancedStock($command, $product);
        } else {
            $this->handleClassicStock($command, $product);
        }
    }

    /**
     * @param UpdateProductStockCommand $command
     * @param Product $product
     *
     * @throws CannotUpdateProductException
     * @throws ProductStockException
     */
    private function handleAdvancedStock(UpdateProductStockCommand $command, Product $product): void
    {
        $productHasAdvancedStock = $this->getProductAdvancedStockEnabled($command, $product);

        $stockAvailable = $this->getOrCreateStockAvailable($command);
        if (null !== $command->dependsOnStock()) {
            if (!$productHasAdvancedStock && $command->dependsOnStock()) {
                throw new ProductStockException(
                    'You cannot perform this action when advanced_stock_management is disabled on the product',
                    ProductStockException::ADVANCED_STOCK_MANAGEMENT_PRODUCT_DISABLED
                );
            }

            $this->checkPackStockType($command, $product);
            $stockAvailable->depends_on_stock = $command->dependsOnStock();
        }

        $this->updateProductField($product, 'advanced_stock_management', $productHasAdvancedStock);
        if (!$productHasAdvancedStock) {
            $stockAvailable->depends_on_stock = false;
        }

        // Now apply updates
        $this->applyPartialUpdate($product, 'stock', CannotUpdateProductException::FAILED_UPDATE_STOCK);
        $this->applyStockAvailableSave($stockAvailable);
        if (null !== $command->dependsOnStock() && $command->dependsOnStock()) {
            StockAvailable::synchronize($product->id);
        }
    }

    /**
     * @param UpdateProductStockCommand $command
     * @param Product $product
     *
     * @throws CannotUpdateProductException
     * @throws ProductStockException
     */
    private function handleClassicStock(UpdateProductStockCommand $command, Product $product): void
    {
        $stockAvailable = $this->getOrCreateStockAvailable($command);

        // Depends on stock is only available in advanced mode
        if (null !== $command->dependsOnStock() && $command->dependsOnStock()) {
            throw new ProductStockException(
                'You cannot perform this action when PS_ADVANCED_STOCK_MANAGEMENT is disabled',
                ProductStockException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
            );
        }
        $product->depends_on_stock = $stockAvailable->depends_on_stock = false;

        if (null !== $command->useAdvancedStockManagement() && $command->useAdvancedStockManagement()) {
            throw new ProductStockException(
                'You cannot perform this action when PS_ADVANCED_STOCK_MANAGEMENT is disabled',
                ProductStockException::ADVANCED_STOCK_MANAGEMENT_CONFIGURATION_DISABLED
            );
        }
        $this->updateProductField($product, 'advanced_stock_management', false);

        if (null !== $command->getPackStockType()) {
            $this->updateProductField($product, 'pack_stock_type', $this->getLegacyPackStockType($command->getPackStockType()->getValue()));
        }

        // Now apply updates
        $this->applyPartialUpdate($product, 'stock', CannotUpdateProductException::FAILED_UPDATE_STOCK);
        $this->applyStockAvailableSave($stockAvailable);
    }

    /**
     * @param StockAvailable $stockAvailable
     *
     * @throws ProductStockException
     */
    private function applyStockAvailableSave(StockAvailable $stockAvailable): void
    {
        try {
            if (false === $stockAvailable->save()) {
                throw new ProductStockException(
                    sprintf(
                        'Failed to update stock available #%s',
                        $stockAvailable->id
                    ),
                    ProductStockException::CANNOT_SAVE_STOCK_AVAILABLE
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductStockException(
                sprintf(
                    'Failed to update stock available #%s',
                    $stockAvailable->id
                ),
                ProductStockException::CANNOT_SAVE_STOCK_AVAILABLE,
                $e
            );
        }
    }

    /**
     * @param UpdateProductStockCommand $command
     *
     * @return StockAvailable
     *
     * @throws ProductStockException
     */
    private function getOrCreateStockAvailable(UpdateProductStockCommand $command): StockAvailable
    {
        // @todo manage combination later (unless it is done in another handler)
        $stockAvailableId = StockAvailable::getStockAvailableIdByProductId($command->getProductId()->getValue());
        // Stock might not be set for this product yet
        if ($stockAvailableId <= 0) {
            $stockAvailable = new StockAvailable();
            $stockAvailable->id_product = $command->getProductId()->getValue();
            $shopParams = [];
            StockAvailable::addSqlShopParams($shopParams);
            $stockAvailable->id_shop = $shopParams['id_shop'] ?? 0;
            $stockAvailable->id_shop_group = $shopParams['id_shop_group'] ?? 0;

            return $stockAvailable;
        }

        try {
            $stockAvailable = new StockAvailable($stockAvailableId);

            if ((int) $stockAvailable->id !== $stockAvailableId) {
                throw new ProductStockException(
                    sprintf(
                        'StockAvailable #%s was not found',
                        $stockAvailableId
                    ),
                    ProductStockException::NOT_FOUND
                );
            }
        } catch (PrestaShopException $e) {
            throw new ProductStockException(
                sprintf('Error occurred when trying to get stock available #%s', $stockAvailableId),
                ProductStockException::NOT_FOUND,
                $e
            );
        }

        return $stockAvailable;
    }

    /**
     * @param UpdateProductStockCommand $command
     * @param Product $product
     *
     * @return bool
     */
    private function getProductAdvancedStockEnabled(
        UpdateProductStockCommand $command,
        Product $product
    ): bool {
        if (null !== $command->useAdvancedStockManagement()) {
            return $command->useAdvancedStockManagement();
        }

        return (bool) $product->advanced_stock_management;
    }

    /**
     * @param UpdateProductStockCommand $command
     * @param Product $product
     *
     * @throws ProductStockException
     */
    private function checkPackStockType(UpdateProductStockCommand $command, Product $product): void
    {
        // If the product doesn't depend on stock or is not a Pack no problem
        if (!$command->dependsOnStock() || !Pack::isPack($product->id)) {
            return;
        }

        // Get pack stock type (or default configuration if needed)
        $packStockType = $product->pack_stock_type;
        if ($packStockType === Pack::STOCK_TYPE_DEFAULT) {
            $packStockType = (int) $this->configuration->get('PS_PACK_STOCK_TYPE');
        }

        // Either the pack has its own stock, or else ALL products from the pack must depend on the stock as well
        if ($packStockType === Pack::STOCK_TYPE_PACK_ONLY || Pack::allUsesAdvancedStockManagement($product->id)) {
            return;
        }

        throw new ProductStockException(
            'You cannot link your pack to product stock because one of them has no advanced stock enabled',
            ProductStockException::INCOMPATIBLE_PACK_STOCK_TYPE
        );
    }

    /**
     * @param string $stockType
     *
     * @return int
     */
    private function getLegacyPackStockType(string $stockType): int
    {
        switch ($stockType) {
            case PackSettings::STOCK_TYPE_PACK_ONLY:
                return Pack::STOCK_TYPE_PACK_ONLY;
            case PackSettings::STOCK_TYPE_PRODUCTS_ONLY:
                return Pack::STOCK_TYPE_PRODUCTS_ONLY;
            case PackSettings::STOCK_TYPE_BOTH:
                return Pack::STOCK_TYPE_PACK_BOTH;
            case PackSettings::STOCK_TYPE_DEFAULT:
            default:
                return Pack::STOCK_TYPE_DEFAULT;
        }
    }
}
