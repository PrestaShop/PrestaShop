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

namespace PrestaShop\PrestaShop\Adapter\Shop\Repository;

use Doctrine\DBAL\Connection;
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

        $result = $qb->execute()->fetchAssociative();
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
            ->execute()
            ->fetchAllAssociative()
        );
    }
}
