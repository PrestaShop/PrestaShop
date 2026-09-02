<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockUpdater;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\CommandHandler\UpdateProductStockHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Updates product stock using legacy object model
 */
#[AsCommandHandler]
class UpdateProductStockAvailableHandler implements UpdateProductStockHandlerInterface
{
    /**
     * @var ProductStockUpdater
     */
    private $productStockUpdater;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @param ProductStockUpdater $productStockUpdater
     * @param ProductRepository $productRepository
     * @param CombinationRepository $combinationRepository
     */
    public function __construct(
        ProductStockUpdater $productStockUpdater,
        ProductRepository $productRepository,
        CombinationRepository $combinationRepository
    ) {
        $this->productStockUpdater = $productStockUpdater;
        $this->combinationRepository = $combinationRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function handle(UpdateProductStockAvailableCommand $command): void
    {
        $productId = $command->getProductId();
        $shopConstraint = $command->getShopConstraint();
        $outOfStockType = $command->getOutOfStockType();

        $stockModification = null;
        if ($command->getDeltaQuantity()) {
            $stockModification = StockModification::buildDeltaQuantity($command->getDeltaQuantity());
        }

        // Now we only fill the properties existing in StockAvailable object model.
        // Other properties related to stock (which exists in Product object model) should be taken care by a unified UpdateProductCommand.
        // For now this will also fill some of deprecated properties in product (quantity, location, out_of_stock),
        // but in future we will remove those fields from Product,
        // and then this handler will only persist StockAvailable related fields as it is designed for.
        $this->productStockUpdater->update(
            $productId,
            new ProductStockProperties(
                $stockModification,
                $outOfStockType,
                $command->getLocation()
            ),
            $shopConstraint
        );

        if (null !== $outOfStockType) {
            if ($shopConstraint->forAllShops() || ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds())) {
                if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
                    $updatedShopIds = $shopConstraint->getShopIds();
                } else {
                    $updatedShopIds = $this->productRepository->getAssociatedShopIds($productId);
                }

                foreach ($updatedShopIds as $shopId) {
                    $this->combinationRepository->updateCombinationOutOfStockType(
                        $productId,
                        $outOfStockType,
                        ShopConstraint::shop($shopId->getValue())
                    );
                }
            } else {
                $this->combinationRepository->updateCombinationOutOfStockType($productId, $outOfStockType, $shopConstraint);
            }
        }
    }
}
