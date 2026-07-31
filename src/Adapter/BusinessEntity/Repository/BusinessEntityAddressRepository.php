<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\BusinessEntity\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

class BusinessEntityAddressRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @param string[] $addressTypes
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAddresses(int $businessEntityId, array $addressTypes): array
    {
        return $this->connection->createQueryBuilder()
            ->select(
                'a.id_address',
                'a.alias',
                'bea.address_type',
                'bea.is_default'
            )
            ->from($this->dbPrefix . 'business_entity_address', 'bea')
            ->innerJoin('bea', $this->dbPrefix . 'address', 'a', 'a.id_address = bea.id_address')
            ->where('bea.id_business_entity = :businessEntityId')
            ->andWhere('bea.address_type IN (:addressTypes)')
            ->andWhere('a.deleted = 0')
            ->orderBy('bea.is_default', 'DESC')
            ->addOrderBy('bea.id_business_entity_address', 'ASC')
            ->setParameter('businessEntityId', $businessEntityId)
            ->setParameter('addressTypes', $addressTypes, ArrayParameterType::STRING)
            ->executeQuery()
            ->fetchAllAssociative();
    }
}
