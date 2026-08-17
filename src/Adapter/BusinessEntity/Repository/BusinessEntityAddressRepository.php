<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\Repository;

use Doctrine\DBAL\Connection;
use PrestaShopBundle\Entity\Enum\AddressTypeEnum;

class BusinessEntityAddressRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Default addresses first, then insertion order, soft-deleted addresses excluded.
     *
     * @return BusinessEntityAddressRow[]
     */
    public function getAddresses(int $businessEntityId): array
    {
        $rows = $this->connection->createQueryBuilder()
            ->select(
                'a.id_address',
                'a.alias',
                'bea.address_type',
                'bea.is_default'
            )
            ->from($this->dbPrefix . 'business_entity_address', 'bea')
            ->innerJoin('bea', $this->dbPrefix . 'address', 'a', 'a.id_address = bea.id_address')
            ->where('bea.id_business_entity = :businessEntityId')
            ->andWhere('a.deleted = 0')
            ->orderBy('bea.is_default', 'DESC')
            ->addOrderBy('bea.id_business_entity_address', 'ASC')
            ->setParameter('businessEntityId', $businessEntityId)
            ->executeQuery()
            ->fetchAllAssociative();

        return array_map(
            static fn (array $row): BusinessEntityAddressRow => new BusinessEntityAddressRow(
                (int) $row['id_address'],
                (string) $row['alias'],
                AddressTypeEnum::from((string) $row['address_type']),
                (bool) $row['is_default'],
            ),
            $rows
        );
    }
}
