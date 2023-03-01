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

namespace PrestaShop\PrestaShop\Core\Domain\Product\Shop\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\DeleteProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\InvalidProductShopAssociationException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;

class SetProductShopsCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var ShopId
     */
    private $sourceShopId;

    /**
     * @var ShopId[]
     */
    private $shopIds;

    /**
     * @param int $productId the product for which the new shop association is being set
     * @param int $sourceShopId the source shop from which product is copied when it is being added to a new shop
     * @param int[] $shopIds ids of shops representing new product-shop association
     */
    public function __construct(
        int $productId,
        int $sourceShopId,
        array $shopIds
    ) {
        $this->setShopIds($shopIds);
        $this->productId = new ProductId($productId);
        $this->sourceShopId = new ShopId($sourceShopId);
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return ShopId
     */
    public function getSourceShopId(): ShopId
    {
        return $this->sourceShopId;
    }

    /**
     * @return ShopId[]
     */
    public function getShopIds(): array
    {
        return $this->shopIds;
    }

    /**
     * @param int[] $shopIds
     */
    private function setShopIds(array $shopIds): void
    {
        if (empty($shopIds)) {
            throw new InvalidProductShopAssociationException(
                sprintf(
                    'Empty shop association provided. Use %s command to delete product instead',
                    DeleteProductCommand::class
                )
            );
        }

        $this->shopIds = array_map(static function (int $shopId): ShopId {
            return new ShopId($shopId);
        }, $shopIds);
    }
}
