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

use PrestaShop\PrestaShop\Adapter\Product\ProductDeleter;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Adapter\Product\Update\ProductShopUpdater;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command\SetProductShopsCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Shop\CommandHandler\SetProductShopsHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class SetProductShopsHandler implements SetProductShopsHandlerInterface
{
    /**
     * @var ShopRepository
     */
    private $shopRepository;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var ProductDeleter
     */
    private $productDeleter;

    /**
     * @var ProductShopUpdater
     */
    private $productShopUpdater;

    public function __construct(
        ShopRepository $shopRepository,
        ProductRepository $productRepository,
        ProductDeleter $productDeleter,
        ProductShopUpdater $productShopUpdater
    ) {
        $this->shopRepository = $shopRepository;
        $this->productRepository = $productRepository;
        $this->productDeleter = $productDeleter;
        $this->productShopUpdater = $productShopUpdater;
    }

    public function handle(SetProductShopsCommand $command): void
    {
        $productId = $command->getProductId();
        $sourceShopId = $command->getSourceShopId();
        $selectedShopIds = $command->getShopIds();

        if (empty($selectedShopIds)) {
            throw new InvalidProductShopAssociationException(
                sprintf(
                    'Empty shop association provided. Use %s command to delete product instead',
                    DeleteProductCommand::class
                )
            );
        }

        $allShopIds = $this->shopRepository->getAllIds();
        $initialShopIds = $this->productRepository->getAssociatedShopIds($productId);

        $this->assertSourceShopIsAlreadyAssociated($sourceShopId, $initialShopIds);

        $shopsToRemove = [];
        $shopsToCopy = [];

        foreach ($allShopIds as $shopId) {
            if ($sourceShopId->getValue() === $shopId->getValue()) {
                // source shop is already associated and we don't allow to unassociate it
                continue;
            }
            if ($this->shopInArray($shopId, $initialShopIds) && !$this->shopInArray($shopId, $selectedShopIds)) {
                $shopsToRemove[] = $shopId;
            } elseif (!$this->shopInArray($shopId, $initialShopIds) && $this->shopInArray($shopId, $selectedShopIds)) {
                $shopsToCopy[] = $shopId;
            }
        }

        // Remove non associated shops
        if (!empty($shopsToRemove)) {
            $this->productDeleter->deleteFromShops($productId, $shopsToRemove);
        }

        // Copy data from source targets
        foreach ($shopsToCopy as $targetShopId) {
            $this->productShopUpdater->copyToShop(
                $productId,
                $sourceShopId,
                $targetShopId
            );
        }
    }

    /**
     * @param ShopId $sourceShopId
     * @param ShopId[] $initialShopIds
     */
    private function assertSourceShopIsAlreadyAssociated(ShopId $sourceShopId, array $initialShopIds): void
    {
        if ($this->shopInArray($sourceShopId, $initialShopIds)) {
            return;
        }

        throw new ShopException(sprintf(
            'Source shopId must be one of current product shops. Got %d',
            $sourceShopId->getValue()
        ));
    }

    /**
     * @param ShopId $searchableShopId
     * @param ShopId[] $shopIds
     *
     * @return bool
     */
    private function shopInArray(ShopId $searchableShopId, array $shopIds): bool
    {
        foreach ($shopIds as $shopId) {
            if ($shopId->getValue() === $searchableShopId->getValue()) {
                return true;
            }
        }

        return false;
    }
}
