<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Store\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotAddStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotDeleteStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotUpdateStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use Store;

class StoreRepository extends AbstractObjectModelRepository
{
    private Connection $connection;
    private string $dbPrefix;

    public function __construct(Connection $connection, string $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @throws CoreException
     * @throws StoreNotFoundException
     */
    public function get(StoreId $storeId): Store
    {
        /** @var Store $store */
        $store = $this->getObjectModel(
            $storeId->getValue(),
            Store::class,
            StoreNotFoundException::class
        );

        return $store;
    }

    /**
     * @throws CannotAddStoreException
     */
    public function add(Store $store): StoreId
    {
        $id = $this->addObjectModel($store, CannotAddStoreException::class);

        return new StoreId($id);
    }

    public function update(Store $store): void
    {
        $this->updateObjectModel($store, CannotUpdateStoreException::class);
    }

    public function partialUpdate(Store $store, array $propertiesToUpdate, int $errorCode): void
    {
        $this->partiallyUpdateObjectModel(
            $store,
            $propertiesToUpdate,
            CannotUpdateStoreException::class,
            $errorCode
        );
    }

    public function delete(StoreId $storeId): void
    {
        $this->deleteObjectModel(
            $this->get($storeId),
            CannotDeleteStoreException::class,
            CannotDeleteStoreException::FAILED_DELETE
        );
    }

    /**
     * Replaces the shop associations for the given store.
     *
     * @param int[] $shopIds
     */
    public function updateShopAssociation(Store $store, array $shopIds): void
    {
        $this->connection->delete(
            $this->dbPrefix . 'store_shop',
            ['id_store' => $store->id]
        );

        if ($shopIds) {
            $store->associateTo($shopIds);
        }
    }

    /**
     * @return ShopId[]
     */
    public function getShopIdsByConstraint(ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopGroupId()) {
            return $this->getAssociatedShopIdsFromGroup($shopConstraint->getShopGroupId());
        }

        if ($shopConstraint->forAllShops()) {
            return array_map(static function (array $result): ShopId {
                return new ShopId((int) $result['id_shop']);
            }, $this->connection->createQueryBuilder()
                ->select('id_shop')
                ->from($this->dbPrefix . 'store_shop', 'ss')
                ->executeQuery()
                ->fetchAllAssociative()
            );
        }

        return [$shopConstraint->getShopId()];
    }

    /**
     * @return ShopId[]
     */
    public function getAssociatedShopIdsFromGroup(ShopGroupId $shopGroupId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ss.id_shop')
            ->from($this->dbPrefix . 'store_shop', 'ss')
            ->innerJoin('ss', $this->dbPrefix . 'shop', 's', 's.id_shop = ss.id_shop')
            ->andWhere('s.id_shop_group = :shopGroupId')
            ->setParameter('shopGroupId', $shopGroupId->getValue())
            ->groupBy('id_shop')
        ;

        return array_map(static function (array $result): ShopId {
            return new ShopId((int) $result['id_shop']);
        }, $qb->executeQuery()->fetchAllAssociative());
    }
}
