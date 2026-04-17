<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Zone\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Zone\ValueObject\ZoneId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;
use Zone;

/**
 * Provides methods to access data storage of Zone
 */
class ZoneRepository extends AbstractObjectModelRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $prefix,
    ) {
    }

    /**
     * @throws ZoneNotFoundException
     */
    public function get(ZoneId $zoneId): Zone
    {
        /** @var Zone $zone */
        $zone = $this->getObjectModel(
            $zoneId->getValue(),
            Zone::class,
            ZoneNotFoundException::class
        );

        return $zone;
    }

    /**
     * @throws ZoneNotFoundException
     */
    public function getZoneIdByCountryId(CountryId $countryId): ZoneId
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('z.id_zone')
            ->from($this->prefix . 'country', 'c')
            ->innerJoin(
                'c',
                $this->prefix . 'zone',
                'z',
                'z.id_zone = c.id_zone'
            )
            ->where('c.id_country = :countryId')
            ->setParameter('countryId', $countryId->getValue())
        ;

        $result = $qb->executeQuery()->fetchAssociative();

        if (!$result) {
            throw new ZoneNotFoundException(sprintf('Zone not found for country %d', $countryId->getValue()));
        }

        return new ZoneId((int) $result['id_zone']);
    }
}
