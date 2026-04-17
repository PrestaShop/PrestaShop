<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\Filler\ProductFillerInterface;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductIndexationUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\UpdateProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\UpdateProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotUpdateProductException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;

/**
 * Handles the @see UpdateProductCommand using legacy object model
 */
#[AsCommandHandler]
class UpdateProductHandler implements UpdateProductHandlerInterface
{
    /**
     * @var ProductFillerInterface
     */
    private $productUpdatablePropertyFiller;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductIndexationUpdater
     */
    private $productIndexationUpdater;

    /**
     * @param ProductFillerInterface $productUpdatablePropertyFiller
     * @param ProductRepository $productRepository
     * @param ProductIndexationUpdater $productIndexationUpdater
     */
    public function __construct(
        ProductFillerInterface $productUpdatablePropertyFiller,
        ProductRepository $productRepository,
        ProductIndexationUpdater $productIndexationUpdater
    ) {
        $this->productUpdatablePropertyFiller = $productUpdatablePropertyFiller;
        $this->productRepository = $productRepository;
        $this->productIndexationUpdater = $productIndexationUpdater;
    }

    /**
     * @param UpdateProductCommand $command
     */
    public function handle(UpdateProductCommand $command): void
    {
        $shopConstraint = $command->getShopConstraint();
        $product = $this->productRepository->getByShopConstraint($command->getProductId(), $shopConstraint);
        $wasVisibleOnSearch = $this->productIndexationUpdater->isVisibleOnSearch($product);
        $wasActive = (bool) $product->active;

        $updatableProperties = $this->productUpdatablePropertyFiller->fillUpdatableProperties(
            $product,
            $command
        );

        if (null !== $command->isActive()) {
            $product->active = $command->isActive();
            $updatableProperties[] = 'active';
        }

        if (empty($updatableProperties)) {
            return;
        }

        $this->productRepository->partialUpdate(
            $product,
            $updatableProperties,
            $shopConstraint,
            CannotUpdateProductException::FAILED_UPDATE_PRODUCT
        );

        if (
            // Reindexing is costly operation, so we check if properties impacting indexation have changed and then reindex if needed.
            $this->productIndexationUpdater->isIndexationNeeded($updatableProperties)
            // If multiple shops are impacted it's safer to update indexation, it's more complicated to check if it's needed
            || $shopConstraint->forAllShops()
            || $shopConstraint->getShopGroupId()
            || ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds())
        ) {
            $this->productIndexationUpdater->updateIndexation($product, $command->getShopConstraint());
        }
    }
}
