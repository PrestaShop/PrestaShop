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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockProperties;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Update\ProductStockUpdater;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Command\UpdateProductStockAvailableCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\CommandHandler\UpdateProductStockHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockModification;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Updates product stock using legacy object model
 */
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
            if ($shopConstraint->forAllShops()) {
                $associatedShopIds = $this->productRepository->getAssociatedShopIds($productId);

                foreach ($associatedShopIds as $shopId) {
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
