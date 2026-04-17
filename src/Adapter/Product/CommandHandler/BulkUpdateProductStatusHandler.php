<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductIndexationUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkUpdateProductStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkUpdateProductStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;

/**
 * Handles command which deletes addresses in bulk action
 */
#[AsCommandHandler]
class BulkUpdateProductStatusHandler extends AbstractBulkHandler implements BulkUpdateProductStatusHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductIndexationUpdater
     */
    private $productIndexationUpdater;

    public function __construct(
        ProductRepository $productRepository,
        ProductIndexationUpdater $productIndexationUpdater
    ) {
        $this->productRepository = $productRepository;
        $this->productIndexationUpdater = $productIndexationUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkUpdateProductStatusCommand $command): void
    {
        $this->handleBulkAction($command->getProductIds(), $command);
    }

    /**
     * @param ProductId $productId
     * @param BulkUpdateProductStatusCommand $command
     *
     * @return void
     */
    protected function handleSingleAction(ProductId $productId, $command = null)
    {
        $product = $this->productRepository->getByShopConstraint($productId, $command->getShopConstraint());
        $wasVisibleOnSearch = $this->productIndexationUpdater->isVisibleOnSearch($product);
        $wasActive = (bool) $product->active;

        $product->active = $command->getNewStatus();
        $this->productRepository->partialUpdate(
            $product,
            ['active'],
            $command->getShopConstraint(),
            CannotUpdateProductException::FAILED_UPDATE_STATUS
        );

        // Reindexing is costly operation, so we check if properties impacting indexation have changed and then reindex if needed.
        $shopConstraint = $command->getShopConstraint();
        if (
            $wasVisibleOnSearch !== $this->productIndexationUpdater->isVisibleOnSearch($product)
            || $wasActive !== (bool) $product->active
            // If multiple shops are impacted it's safer to update indexation, it's more complicated to check if it's needed
            || $shopConstraint->forAllShops()
            || $shopConstraint->getShopGroupId()
            || ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds())
        ) {
            $this->productIndexationUpdater->updateIndexation($product, $shopConstraint);
        }
    }

    protected function buildBulkException(): BulkProductException
    {
        return new CannotBulkUpdateProductException();
    }
}
