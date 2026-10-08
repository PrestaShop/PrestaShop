<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Shop\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotAddShopUrlException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotDeleteShopUrlException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\CannotUpdateShopUrlException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopUrlNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopUrlId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use ShopUrl;

final class ShopUrlRepository extends AbstractObjectModelRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @throws ShopUrlNotFoundException
     */
    public function get(ShopUrlId $shopUrlId): ShopUrl
    {
        /** @var ShopUrl $shopUrl */
        $shopUrl = $this->getObjectModel($shopUrlId->getValue(), ShopUrl::class, ShopUrlNotFoundException::class);

        return $shopUrl;
    }

    public function add(ShopUrl $shopUrl): ShopUrlId
    {
        return new ShopUrlId($this->addObjectModel($shopUrl, CannotAddShopUrlException::class));
    }

    /**
     * @param string[] $propertiesToUpdate
     */
    public function partialUpdate(ShopUrl $shopUrl, array $propertiesToUpdate): void
    {
        $this->partiallyUpdateObjectModel($shopUrl, $propertiesToUpdate, CannotUpdateShopUrlException::class);
    }

    public function delete(ShopUrl $shopUrl): void
    {
        $this->deleteObjectModel($shopUrl, CannotDeleteShopUrlException::class, CannotDeleteShopUrlException::FAILED_DELETE);
    }

    public function setMain(ShopUrl $shopUrl): void
    {
        if (!$shopUrl->setMain()) {
            throw new CannotUpdateShopUrlException(sprintf('Failed to set shop url %d as main', $shopUrl->id));
        }
    }

    public function hasMainUrl(ShopId $shopId): bool
    {
        return false !== $this->connection->createQueryBuilder()
            ->select('su.id_shop_url')
            ->from($this->dbPrefix . 'shop_url', 'su')
            ->where('su.id_shop = :shopId')
            ->andWhere('su.main = 1')
            ->setParameter('shopId', $shopId->getValue())
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
    }

    public function hasBaseUrl(ShopId $shopId, bool $secure): bool
    {
        return false !== $this->connection->createQueryBuilder()
            ->select('su.id_shop_url')
            ->from($this->dbPrefix . 'shop', 's')
            ->innerJoin('s', $this->dbPrefix . 'shop_url', 'su', 'su.id_shop = s.id_shop')
            ->where('s.id_shop = :shopId')
            ->andWhere('s.active = 1')
            ->andWhere('s.deleted = 0')
            ->andWhere('su.main = 1')
            ->andWhere(($secure ? 'su.domain_ssl' : 'su.domain') . " <> ''")
            ->setParameter('shopId', $shopId->getValue())
            ->setMaxResults(1)
            ->executeQuery()
            ->fetchOne();
    }
}
