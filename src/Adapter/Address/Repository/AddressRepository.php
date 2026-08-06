<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Address\Repository;

use Address;
use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Address\ValueObject\AddressId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\Attribute\Exception\AttributeNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractMultiShopObjectModelRepository;

/**
 * Provides access to address data source
 */
class AddressRepository extends AbstractMultiShopObjectModelRepository
{
    public function __construct(
        private readonly Connection $connection,
        private string $dbPrefix,
    ) {
    }

    /**
     * @param AddressId $addressId
     *
     * @return Address
     *
     * @throws AttributeNotFoundException
     * @throws CoreException
     */
    public function get(AddressId $addressId): Address
    {
        /** @var Address $address */
        $address = $this->getObjectModel(
            $addressId->getValue(),
            Address::class,
            AddressNotFoundException::class
        );

        return $address;
    }

    public function getZoneId(AddressId $addressId): int
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('s.id_zone AS id_zone_state', 'c.id_zone')
            ->from($this->dbPrefix . 'address', 'a')
            ->leftJoin('a', $this->dbPrefix . 'country', 'c', 'c.id_country = a.id_country')
            ->leftJoin('a', $this->dbPrefix . 'state', 's', 's.id_state = a.id_state')
            ->where('a.id_address = :addressId')
            ->setParameter('addressId', $addressId->getValue())
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        if ($result === false || (empty($result['id_zone_state']) && empty($result['id_zone']))) {
            throw new AddressNotFoundException(sprintf('Address with id %s not found', $addressId->getValue()));
        }

        return !empty($result['id_zone_state'])
            ? (int) $result['id_zone_state']
            : (int) $result['id_zone'];
    }
}
