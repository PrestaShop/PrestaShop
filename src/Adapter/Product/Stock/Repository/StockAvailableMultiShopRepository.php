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

namespace PrestaShop\PrestaShop\Adapter\Product\Stock\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Product\Stock\Validate\StockAvailableValidator;
use PrestaShop\PrestaShop\Core\Domain\OrderState\ValueObject\OrderStateId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotAddStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotDeleteStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\CannotUpdateStockAvailableException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\Exception\StockAvailableNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Stock\ValueObject\StockId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;
use PrestaShopException;
use StockAvailable;

/**
 * @todo: This class has been added while we progressively migrate each domain to multishop It is used for product
 *        commands for now it wille need to be integrated in combination commands later At that point both repositories
 *        can be merged into one (with StockAvailableRepository) which is used everywhere and we can clean the double repositories.
 */
class StockAvailableMultiShopRepository extends AbstractMultiShopObjectModelRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var StockAvailableValidator
     */
    private $stockAvailableValidator;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param StockAvailableValidator $stockAvailableValidator
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        StockAvailableValidator $stockAvailableValidator
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->stockAvailableValidator = $stockAvailableValidator;
    }

    /**
     * @param StockAvailable $stockAvailable
     *
     * @throws CoreException
     */
    public function update(StockAvailable $stockAvailable): void
    {
        $this->stockAvailableValidator->validate($stockAvailable);
        $this->updateObjectModel($stockAvailable, CannotUpdateStockAvailableException::class);
    }

    /**
     * @param StockId $stockId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function get(StockId $stockId): StockAvailable
    {
        /** @var StockAvailable $stockAvailable */
        $stockAvailable = $this->getObjectModel(
            $stockId->getValue(),
            StockAvailable::class,
            StockAvailableNotFoundException::class
        );

        return $stockAvailable;
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return StockId
     *
     * @throws StockAvailableNotFoundException
     */
    public function getStockIdByProduct(ProductId $productId, ShopId $shopId): StockId
    {
        $stockAvailableId = StockAvailable::getStockAvailableIdByProductId($productId->getValue(), null, $shopId->getValue());
        if ($stockAvailableId <= 0) {
            throw new StockAvailableNotFoundException(sprintf(
                    'Cannot find StockAvailable for product #%d',
                    $productId->getValue()
                )
            );
        }

        return new StockId($stockAvailableId);
    }

    /**
     * @param ProductId $productId
     * @param ShopId $shopId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function getForProduct(ProductId $productId, ShopId $shopId): StockAvailable
    {
        $stockId = $this->getStockIdByProduct($productId, $shopId);

        return $this->get($stockId);
    }

    /**
     * @param ProductId $productId
     *
     * @throws CoreException
     */
    public function delete(ProductId $productId, ShopId $shopId): void
    {
        $this->deleteObjectModel($this->getForProduct($productId, $shopId), CannotDeleteStockAvailableException::class);
    }

    /**
     * @param CombinationId $combinationId
     * @param ShopId $shopId
     *
     * @return StockId
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function getStockIdByCombination(CombinationId $combinationId, ShopId $shopId): StockId
    {
        //@todo: add shop conditions based on shop group sharing stock or not. like in ProductCombinationQueryBuilder
        $row = $this
            ->connection
            ->createQueryBuilder()
            ->select('id_stock_available')
            ->from($this->dbPrefix . 'stock_available')
            ->where(
                'id_product_attribute = :combinationId',
                'id_shop = :shopId'
            )
            ->setParameter('combinationId', $combinationId->getValue())
            ->setParameter('shopId', $shopId->getValue())
            ->execute()
            ->fetch()
        ;
        if (empty($row)) {
            throw new StockAvailableNotFoundException(
                sprintf(
                    'Cannot find StockAvailable for combination #%d',
                    $combinationId->getValue()
                )
            );
        }

        return new StockId((int) $row['id_stock_available']);
    }

    /**
     * @param CombinationId $combinationId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function getForCombination(CombinationId $combinationId, ShopId $shopId): StockAvailable
    {
        $stockId = $this->getStockIdByCombination($combinationId, $shopId);

        return $this->get($stockId);
    }

    /**
     * @param ProductId $productId
     * @param CombinationId|null $combinationId
     *
     * @return StockAvailable
     *
     * @throws CoreException
     * @throws StockAvailableNotFoundException
     */
    public function createStockAvailable(ProductId $productId, ShopId $shopId, ?CombinationId $combinationId = null): StockAvailable
    {
        $stockAvailable = new StockAvailable();
        $stockAvailable->id_product = $productId->getValue();
        $stockAvailable->id_product_attribute = $combinationId ? $combinationId->getValue() : NoCombinationId::NO_COMBINATION_ID;

        // Use legacy method, it checks if the shop belongs to a ShopGroup that shares stock, in which case the StockAvailable
        // must be assigned to the group not the shop
        $shopParams = [];
        try {
            StockAvailable::addSqlShopParams($shopParams, $shopId->getValue());
        } catch (PrestaShopException $e) {
            throw new CoreException(
                sprintf('Error occurred when trying to add StockAvailable shop params #%d', $productId->getValue()),
                0,
                $e
            );
        }

        if (empty($shopParams['id_shop']) && empty($shopParams['id_shop_group'])) {
            throw new CannotAddStockAvailableException('StockAvailable must be assigned to a shop or a shop group');
        }

        $stockAvailable->id_shop = $shopParams['id_shop'] ?? 0;
        $stockAvailable->id_shop_group = $shopParams['id_shop_group'] ?? 0;
        $this->addObjectModel($stockAvailable, CannotAddStockAvailableException::class);

        return $stockAvailable;
    }

    /**
     * @param ProductId $productId
     * @param CombinationIdInterface $combinationId
     *
     * @return StockId[]
     */
    public function getAllShopsStockIds(ProductId $productId, CombinationIdInterface $combinationId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('id_stock_available')
            ->from($this->dbPrefix . 'stock_available', 'sa')
            ->where('sa.id_product = :productId AND sa.id_product_attribute = :combinationId')
            ->setParameter('productId', $productId->getValue())
            ->setParameter('combinationId', $combinationId->getValue())
        ;

        return array_map(static function (array $stock) {
            return new StockId((int) $stock['id_stock_available']);
        }, $qb->execute()->fetchAllAssociative());
    }

    /**
     * Updates the physical_quantity and reserved_quantity columns for the specified Stock. Most of this function logic comes from
     * StockManager::updatePhysicalProductQuantity
     *
     * @param StockId $stockId
     * @param OrderStateId $errorStateId
     * @param OrderStateId $canceledStateId
     */
    public function updatePhysicalProductQuantity(StockId $stockId, OrderStateId $errorStateId, OrderStateId $canceledStateId): void
    {
        $this->updateReservedProductQuantity($stockId, $errorStateId, $canceledStateId);

        // Now update the physical_quantity
        $updateQb = $this->connection->createQueryBuilder();
        $updateQb
            ->update($this->dbPrefix . 'stock_available', 'sa')
            ->set('physical_quantity', 'sa.quantity + sa.reserved_quantity')
            ->where('sa.id_stock_available = :stockId')
            ->setParameter('stockId', $stockId->getValue())
        ;
        $updateQb->execute();
    }

    protected function updateReservedProductQuantity(StockId $stockId, OrderStateId $errorStateId, OrderStateId $canceledStateId): void
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->addSelect('SUM(od.product_quantity - od.product_quantity_refunded) AS reserved_quantity')
            ->from($this->dbPrefix . 'orders', 'o')
            ->innerJoin('o', $this->dbPrefix . 'order_detail', 'od', 'od.id_order = o.id_order')
            ->innerJoin('o', $this->dbPrefix . 'order_state', 'os', 'os.id_order_state = o.current_state')
            ->innerJoin(
                'od', $this->dbPrefix . 'stock_available', 'sa',
                'od.product_id = sa.id_product AND od.product_attribute_id = sa.id_product_attribute AND od.id_shop = sa.id_shop'
            )
            ->where($qb->expr()->and(
                $qb->expr()->eq('o.id_shop', 'sa.id_shop'),
                $qb->expr()->neq('os.shipped', 1),
                $qb->expr()->or(
                    $qb->expr()->eq('o.valid', 1),
                    $qb->expr()->and(
                        $qb->expr()->neq('os.id_order_state', ':errorStateId'),
                        $qb->expr()->neq('os.id_order_state', ':canceledStateId')
                    )
                ),
                $qb->expr()->eq('sa.id_stock_available', ':stockId')
            ))
            ->groupBy('od.product_id', 'od.product_attribute_id')
            ->setParameters([
                'stockId' => $stockId->getValue(),
                'errorStateId' => $errorStateId->getValue(),
                'canceledStateId' => $canceledStateId->getValue(),
            ])
        ;

        $result = $qb->execute()->fetchAssociative();
        $reservedQuantity = (int) ($result['reserved_quantity'] ?? 0);

        if ($reservedQuantity > 0) {
            $updateQb = $this->connection->createQueryBuilder();
            $updateQb
                ->update($this->dbPrefix . 'stock_available', 'sa')
                ->set('reserved_quantity', (string) $reservedQuantity)
                ->where('sa.id_stock_available = :stockId')
                ->setParameter('stockId', $stockId->getValue())
            ;
            $updateQb->execute();
        }
    }
}
