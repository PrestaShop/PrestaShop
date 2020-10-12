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

namespace PrestaShop\PrestaShop\Adapter\Product\Repository;

use PrestaShop\PrestaShop\Adapter\AbstractObjectModelRepository;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductStockException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use StockAvailable;

class StockAvailableRepository extends AbstractObjectModelRepository
{
    /**
     * @param ProductId $productId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     */
    public function getOrCreate(ProductId $productId): StockAvailable
    {
        $stockAvailableId = StockAvailable::getStockAvailableIdByProductId($productId->getValue());
        if ($stockAvailableId <= 0) {
            return $this->createStockAvailable($productId);
        }

        return $this->getStockAvailable($stockAvailableId);
    }

    /**
     * @param ProductId $productId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     */
    public function get(ProductId $productId): StockAvailable
    {
        $stockAvailableId = StockAvailable::getStockAvailableIdByProductId($productId->getValue());
        if ($stockAvailableId <= 0) {
            throw new ProductStockException(sprintf(
                    'Cannot find StockAvailable for product %d',
                    $productId->getValue()
                ),
                ProductStockException::NOT_FOUND
            );
        }

        return $this->getStockAvailable($stockAvailableId);
    }

    /**
     * @param int $stockAvailableId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     */
    private function getStockAvailable(int $stockAvailableId): StockAvailable
    {
        /** @var StockAvailable $product */
        $stockAvailable = $this->getObjectModel(
            $stockAvailableId,
            StockAvailable::class,
            ProductStockException::class,
            ProductStockException::NOT_FOUND
        );

        return $stockAvailable;
    }

    /**
     * @param ProductId $productId
     *
     * @return StockAvailable
     */
    private function createStockAvailable(ProductId $productId): StockAvailable
    {
        $stockAvailable = new StockAvailable();
        $stockAvailable->id_product = $productId->getValue();
        $shopParams = [];
        StockAvailable::addSqlShopParams($shopParams);
        $stockAvailable->id_shop = $shopParams['id_shop'] ?? 0;
        $stockAvailable->id_shop_group = $shopParams['id_shop_group'] ?? 0;

        return $stockAvailable;
    }
}
