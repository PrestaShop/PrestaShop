<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotAddShopGroupException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopGroupException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopGroupException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopGroupNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopGroupId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use ShopGroup;

/**
 * Provides methods to access data storage for shopGroup
 */
class ShopGroupRepository extends AbstractObjectModelRepository
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
     * @param ShopGroupId $shopGroupId
     *
     * @return ShopGroup
     *
     * @throws ShopGroupNotFoundException
     */
    public function get(ShopGroupId $shopGroupId): ShopGroup
    {
        /** @var ShopGroup $shop */
        $shop = $this->getObjectModel(
            $shopGroupId->getValue(),
            ShopGroup::class,
            ShopGroupNotFoundException::class
        );

        return $shop;
    }

    public function add(ShopGroup $shopGroup): ShopGroupId
    {
        return new ShopGroupId($this->addObjectModel($shopGroup, CannotAddShopGroupException::class));
    }

    /**
     * @param string[] $propertiesToUpdate
     */
    public function partialUpdate(ShopGroup $shopGroup, array $propertiesToUpdate): void
    {
        $this->partiallyUpdateObjectModel($shopGroup, $propertiesToUpdate, CannotUpdateShopGroupException::class);
    }

    public function delete(ShopGroup $shopGroup): void
    {
        $this->deleteObjectModel($shopGroup, CannotDeleteShopGroupException::class);
    }

    /**
     * @param ShopId $shopId
     *
     * @return ShopGroup
     *
     * @throws ShopGroupNotFoundException
     * @throws ShopNotFoundException
     */
    public function getByShop(ShopId $shopId): ShopGroup
    {
        return $this->get($this->getShopGroupIdByShopId($shopId));
    }

    /**
     * @param ShopId $shopId
     *
     * @return ShopGroupId
     *
     * @throws ShopNotFoundException
     */
    public function getShopGroupIdByShopId(ShopId $shopId): ShopGroupId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('s.id_shop_group')
            ->from($this->dbPrefix . 'shop', 's')
            ->where('s.id_shop = :shopId')
            ->setParameter('shopId', $shopId->getValue())
        ;

        $result = $qb->executeQuery()->fetchAssociative();
        if (false === $result) {
            throw new ShopNotFoundException(sprintf('Could not find shop with id %d', $shopId->getValue()));
        }

        return new ShopGroupId((int) $result['id_shop_group']);
    }

    /**
     * @param ShopGroupId $shopGroupId
     *
     * @throws ShopGroupNotFoundException
     */
    public function assertShopGroupExists(ShopGroupId $shopGroupId): void
    {
        parent::assertObjectModelExists(
            $shopGroupId->getValue(),
            'shop_group',
            ShopGroupNotFoundException::class
        );
    }

    /**
     * @param ShopGroupId $shopGroupId
     *
     * @return ShopId[]
     */
    public function getShopsFromGroup(ShopGroupId $shopGroupId): array
    {
        return array_map(static function (array $shop) {
            return new ShopId((int) $shop['id_shop']);
        }, $this->connection
            ->createQueryBuilder()
            ->select('s.id_shop')
            ->from($this->dbPrefix . 'shop', 's')
            ->where('s.id_shop_group = :shopGroupId')
            ->setParameter('shopGroupId', $shopGroupId->getValue())
            ->executeQuery()
            ->fetchAllAssociative()
        );
    }

    /**
     * @param int[]|null $shopIds limits the tree to these shops and to the groups containing them
     *
     * @return array<int, array{id: int, name: string, shops: array<int, array{id: int, name: string, urls: list<array{id: int, url: string}>}>}>
     */
    public function getShopTree(?array $shopIds = null): array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('sg.id_shop_group, sg.name AS group_name, s.id_shop, s.name AS shop_name, su.id_shop_url')
            ->addSelect('CONCAT(su.domain, su.physical_uri, su.virtual_uri) AS url')
            ->from($this->dbPrefix . 'shop_group', 'sg')
            ->leftJoin('sg', $this->dbPrefix . 'shop', 's', 's.id_shop_group = sg.id_shop_group AND s.deleted = 0')
            ->leftJoin('s', $this->dbPrefix . 'shop_url', 'su', 'su.id_shop = s.id_shop')
            ->where('sg.deleted = 0')
            ->orderBy('sg.id_shop_group')
            ->addOrderBy('s.id_shop')
            ->addOrderBy('su.id_shop_url')
        ;

        if (null !== $shopIds) {
            $qb
                ->andWhere('s.id_shop IN (:shopIds)')
                ->setParameter('shopIds', $shopIds, ArrayParameterType::INTEGER)
            ;
        }

        $tree = [];
        foreach ($qb->executeQuery()->fetchAllAssociative() as $row) {
            $groupId = (int) $row['id_shop_group'];
            $tree[$groupId] ??= ['id' => $groupId, 'name' => $row['group_name'], 'shops' => []];

            if (null === $row['id_shop']) {
                continue;
            }
            $shopId = (int) $row['id_shop'];
            $tree[$groupId]['shops'][$shopId] ??= ['id' => $shopId, 'name' => $row['shop_name'], 'urls' => []];

            if (null !== $row['id_shop_url']) {
                $tree[$groupId]['shops'][$shopId]['urls'][] = ['id' => (int) $row['id_shop_url'], 'url' => $row['url']];
            }
        }

        return $tree;
    }
}
