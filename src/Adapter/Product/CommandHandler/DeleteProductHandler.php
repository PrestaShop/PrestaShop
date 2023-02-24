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

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\DeleteProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

/**
 * Handles @see DeleteProductCommand using legacy object model
 */
class DeleteProductHandler implements DeleteProductHandlerInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductImageMultiShopRepository
     */
    private $productImageRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @param ProductRepository $productRepository
     * @param ProductImageMultiShopRepository $productImageRepository
     * @param CombinationRepository $combinationRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductImageMultiShopRepository $productImageRepository,
        CombinationRepository $combinationRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productImageRepository = $productImageRepository;
        $this->combinationRepository = $combinationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteProductCommand $command): void
    {
        $productId = $command->getProductId();
        $shopConstraint = $command->getShopConstraint();

        $this->removeImages(
            $productId,
            $this->productRepository->getShopIdsByConstraint($productId, $shopConstraint)
        );
        $this->removeCombinations($productId, $shopConstraint);
        $this->productRepository->deleteByShopConstraint($productId, $shopConstraint);
    }

    /**
     * @param ProductId $productId
     * @param ShopId[] $shopIds
     */
    private function removeImages(ProductId $productId, array $shopIds): void
    {
        foreach ($shopIds as $shopId) {
            $imageIds = $this->productImageRepository->getImageIds($productId, ShopConstraint::shop($shopId->getValue()));
            foreach ($imageIds as $imageId) {
                $this->productImageRepository->deleteFromShops($imageId, [$shopId]);
            }
        }
    }

    private function removeCombinations(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        if (!$this->productRepository->hasCombinations($productId)) {
            return;
        }

        $shopIds = $this->productRepository->getShopIdsByConstraint($productId, $shopConstraint);
        foreach ($shopIds as $shopId) {
            $this->combinationRepository->deleteByProductId($productId, ShopConstraint::shop($shopId->getValue()));
        }
    }
}
