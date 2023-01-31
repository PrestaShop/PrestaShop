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

namespace PrestaShop\PrestaShop\Adapter\Product\Shop\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\CommandHandler\AbstractBulkHandler;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductMultiShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductFromShopsException;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\BulkDeleteProductFromShopsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\CommandHandler\BulkDeleteProductFromShopsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Handles command which deletes products by shop constraint in bulk action
 */
class BulkDeleteProductFromShopsHandler extends AbstractBulkHandler implements BulkDeleteProductFromShopsHandlerInterface
{
    /**
     * @var ProductMultiShopRepository
     */
    private $productRepository;

    /**
     * @var CombinationRepository
     */
    private $combinationRepository;

    /**
     * @var ProductImageMultiShopRepository
     */
    private $productImageMultiShopRepository;

    /**
     * @param ProductMultiShopRepository $productRepository
     */
    public function __construct(
        ProductMultiShopRepository $productRepository,
        CombinationRepository $combinationMultiShopRepository,
        ProductImageMultiShopRepository $productImageMultiShopRepository
    ) {
        $this->productRepository = $productRepository;
        $this->combinationRepository = $combinationMultiShopRepository;
        $this->productImageMultiShopRepository = $productImageMultiShopRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(BulkDeleteProductFromShopsCommand $command): void
    {
        $this->handleBulkAction($command->getProductIds(), $command);
    }

    /**
     * @param ProductId $productId
     * @param BulkDeleteProductFromShopsCommand $command
     *
     * @return void
     */
    protected function handleSingleAction(ProductId $productId, $command = null)
    {
        $this->removeImages($productId, $command->getShopConstraint());

        if ($this->productRepository->hasCombinations($productId)) {
            $this->combinationRepository->deleteByProductId($productId, $command->getShopConstraint());
        }

        $this->productRepository->deleteByShopConstraint($productId, $command->getShopConstraint());
    }

    protected function buildBulkException(): BulkProductException
    {
        return new CannotBulkDeleteProductFromShopsException();
    }

    /**
     * @param ProductId $productId
     * @param ShopConstraint $shopConstraint
     */
    private function removeImages(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        $imageIds = $this->productImageMultiShopRepository->getImagesIds($productId, $shopConstraint);
        foreach ($imageIds as $imageId) {
            $this->productImageMultiShopRepository->deleteByShopConstraint($imageId, $shopConstraint);
        }
    }
}
