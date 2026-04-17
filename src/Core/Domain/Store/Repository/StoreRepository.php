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
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotDeleteStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\CannotUpdateStoreException;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Store\ValueObject\StoreId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use Store;

/**
 * Methods to access data source of Store
 */
class StoreRepository extends AbstractObjectModelRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param StoreId $storeId
     *
     * @return Store
     *
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
     * @param Store $store
     * @param array $propertiesToUpdate
     * @param int $errorCode
     */
    public function partialUpdate(Store $store, array $propertiesToUpdate, int $errorCode): void
    {
        $this->partiallyUpdateObjectModel(
            $store,
            $propertiesToUpdate,
            CannotUpdateStoreException::class,
            $errorCode
        );
    }

    /**
     * @param StoreId $storeId
     */
    public function delete(StoreId $storeId): void
    {
        $this->deleteObjectModel(
            $this->get($storeId),
            CannotDeleteStoreException::class,
            CannotDeleteStoreException::FAILED_DELETE
        );
    }

    /**
     * @param ShopConstraint $shopConstraint
     *
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
     * @param ShopGroupId $shopGroupId
     *
     * @return ShopId[]
     */
    public function getAssociatedShopIdsFromGroup(ShopGroupId $shopGroupId): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('ss.id_shop')
            ->from($this->dbPrefix . 'store_shop', 'ss')
            ->innerJoin(
                'ss',
                $this->dbPrefix . 'shop',
                's',
                's.id_shop = ss.id_shop'
            )
            ->andWhere('s.id_shop_group = :shopGroupId')
            ->setParameter('shopGroupId', $shopGroupId->getValue())
            ->groupBy('id_shop')
        ;

        return array_map(static function (array $result): ShopId {
            return new ShopId((int) $result['id_shop']);
        }, $qb->executeQuery()->fetchAllAssociative());
    }
}
