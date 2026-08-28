<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopCollection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use Shop;

/**
 * Provides methods to access data storage for shop
 */
class ShopRepository extends AbstractObjectModelRepository
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
     * @param ShopId $shopId
     *
     * @return Shop
     *
     * @throws ShopNotFoundException
     */
    public function get(ShopId $shopId): Shop
    {
        /** @var Shop $shop */
        $shop = $this->getObjectModel(
            $shopId->getValue(),
            Shop::class,
            ShopNotFoundException::class
        );

        return $shop;
    }

    public function getShopName(ShopId $shopId): string
    {
        $result = $this
            ->connection
            ->createQueryBuilder()
            ->select('s.name')
            ->from($this->dbPrefix . 'shop', 's')
            ->where('s.id_shop = :shopId')
            ->setParameter('shopId', $shopId->getValue())
            ->executeQuery()
            ->fetchAssociative()
        ;

        if (empty($result['name'])) {
            throw new ShopNotFoundException(sprintf('%s #%d was not found', Shop::class, $shopId->getValue()));
        }

        return $result['name'];
    }

    /**
     * @param ShopId $shopId
     *
     * @throws ShopNotFoundException
     */
    public function assertShopExists(ShopId $shopId): void
    {
        parent::assertObjectModelExists(
            $shopId->getValue(),
            'shop',
            ShopNotFoundException::class
        );
    }

    /**
     * Returns every shop id covered by the constraint: single shop → [id]; ShopCollection
     * → its ids; shop group → the group's shops; all shops → every shop. Group and
     * all-shops scopes only contain usable shops — soft-deleted (`deleted = 1`) and
     * inactive (`active = 0`) shops are excluded, matching the native
     * Shop::getContextListShopID() fan-out — and are returned ordered by shop id.
     * Explicit ids (single shop, ShopCollection) are returned as given, without existence
     * or state checks: naming a shop is the caller's responsibility.
     *
     * This is the single query implementation; ShopListResolver memoizes over it.
     *
     * @return int[]
     */
    public function getAssociatedShopIds(ShopConstraint $shopConstraint): array
    {
        if ($shopConstraint->getShopId()) {
            return [$shopConstraint->getShopId()->getValue()];
        }

        if ($shopConstraint instanceof ShopCollection && $shopConstraint->hasShopIds()) {
            return array_map(static fn (ShopId $shopId): int => $shopId->getValue(), $shopConstraint->getShopIds());
        }

        $qb = $this
            ->connection
            ->createQueryBuilder()
            ->select('s.id_shop')
            ->from($this->dbPrefix . 'shop', 's')
            ->andWhere('s.deleted = 0')
            ->andWhere('s.active = 1')
            ->orderBy('s.id_shop', 'ASC')
        ;

        if ($shopConstraint->getShopGroupId()) {
            $qb
                ->andWhere('s.id_shop_group = :shopGroupId')
                ->setParameter('shopGroupId', $shopConstraint->getShopGroupId()->getValue())
            ;
        }

        $result = $qb->executeQuery()->fetchAllAssociative();

        return array_map(fn (array $shopRow) => (int) $shopRow['id_shop'], $result);
    }

    public function getAllShopIds(): array
    {
        $qb = $this
            ->connection
            ->createQueryBuilder()
            ->select('s.id_shop')
            ->from($this->dbPrefix . 'shop', 's')
        ;

        $result = $qb->executeQuery()->fetchAllAssociative();

        return array_map(fn (array $shopRow) => (int) $shopRow['id_shop'], $result);
    }
}
