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
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotAddStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotUpdateStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShopException;
use StockAvailable;

/**
 * Methods to handle StockAvailable data storage
 */
class StockAvailableRepository extends AbstractObjectModelRepository
{
    /**
     * @param StockAvailable $stockAvailable
     *
     * @throws CoreException
     */
    public function update(StockAvailable $stockAvailable): void
    {
        $this->updateObjectModel($stockAvailable, CannotUpdateStockAvailableException::class);
    }

    /**
     * @param ProductId $productId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function get(ProductId $productId): StockAvailable
    {
        $stockAvailableId = StockAvailable::getStockAvailableIdByProductId($productId->getValue());
        if ($stockAvailableId <= 0) {
            throw new StockAvailableNotFoundException(sprintf(
                    'Cannot find StockAvailable for product %d',
                    $productId->getValue()
                )
            );
        }

        /** @var StockAvailable $stockAvailable */
        $stockAvailable = $this->getObjectModel(
            $stockAvailableId,
            StockAvailable::class,
            StockAvailableNotFoundException::class
        );

        return $stockAvailable;
    }

    /**
     * @param ProductId $productId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function create(ProductId $productId): StockAvailable
    {
        $stockAvailable = new StockAvailable();
        $stockAvailable->id_product = $productId->getValue();
        $shopParams = [];
        try {
            StockAvailable::addSqlShopParams($shopParams);
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to add StockAvailable shop params #%d', $productId->getValue()),
                0,
                $e
            );
        }

        $stockAvailable->id_shop = $shopParams['id_shop'] ?? 0;
        $stockAvailable->id_shop_group = $shopParams['id_shop_group'] ?? 0;
        $this->addObjectModel($stockAvailable, CannotAddStockAvailableException::class);

        return $stockAvailable;
    }
}
