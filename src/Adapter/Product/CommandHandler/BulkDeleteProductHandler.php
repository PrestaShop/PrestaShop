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

use InvalidArgumentException;
use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageMultiShopRepository;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\BulkDeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\CommandHandler\BulkDeleteProductHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\BulkProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\CannotBulkDeleteProductException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Handles command which deletes products in bulk action
 */
final class BulkDeleteProductHandler extends AbstractBulkHandler implements BulkDeleteProductHandlerInterface
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
    public function handle(BulkDeleteProductCommand $command): void
    {
        $this->handleBulkAction($command->getProductIds(), $command);
    }

    /**
     * @param ProductId $productId
     * @param BulkDeleteProductCommand|null $command
     *
     * @return void
     */
    protected function handleSingleAction(ProductId $productId, $command = null): void
    {
        if (!($command instanceof BulkDeleteProductCommand)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Expected argument $command of type "%s". Got "%s"',
                    BulkDeleteProductCommand::class,
                    var_export($command, true)
                ));
        }

        $shopConstraint = $command->getShopConstraint();

        $this->removeImages($productId, $shopConstraint);
        $this->removeCombinations($productId, $shopConstraint);
        $this->productRepository->deleteByShopConstraint($productId, $shopConstraint);
    }

    protected function buildBulkException(): BulkProductException
    {
        return new CannotBulkDeleteProductException();
    }

    private function removeImages(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        $imageIds = $this->productImageRepository->getImageIds($productId, $shopConstraint);
        foreach ($imageIds as $imageId) {
            $this->productImageRepository->deleteByShopConstraint($imageId, $shopConstraint);
        }
    }

    private function removeCombinations(ProductId $productId, ShopConstraint $shopConstraint): void
    {
        if (!$this->productRepository->hasCombinations($productId)) {
            return;
        }

        $this->combinationRepository->deleteByProductId($productId, $shopConstraint);
    }
}
