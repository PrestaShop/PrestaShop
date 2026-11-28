<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\CommandHandler\AbstractBulkHandler;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\BulkUpdateProductOutOfStockTypeCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\CommandHandler\BulkUpdateProductOutOfStockTypeHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Applies one out of stock behavior to every selected product.
 */
#[AsCommandHandler]
class BulkUpdateProductOutOfStockTypeHandler extends AbstractBulkHandler implements BulkUpdateProductOutOfStockTypeHandlerInterface
{
    public function __construct(
        private readonly ProductStockUpdater $productStockUpdater,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkUpdateProductOutOfStockTypeCommand $command): void
    {
        $this->handleBulkAction($command->getProductIds(), $command);
    }

    /**
     * WHY this delegates instead of writing the field: the behavior is stored twice, on
     * `product.out_of_stock` and on `stock_available.out_of_stock`, and the front office reads the
     * second one - `Product::loadStockData()` overwrites the product's copy with it. ProductStockUpdater
     * already writes both and already resolves the shop constraint to the right stock rows, so the bulk
     * action stays exactly as correct as editing one product in the Stock tab.
     *
     * @param BulkUpdateProductOutOfStockTypeCommand $command
     */
    protected function handleSingleAction(ProductId $productId, $command = null): void
    {
        $this->productStockUpdater->update(
            $productId,
            new ProductStockProperties(null, $command->getOutOfStockType()),
            $command->getShopConstraint()
        );
    }
}
