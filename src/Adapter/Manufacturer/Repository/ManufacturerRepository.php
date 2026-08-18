<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\ManufacturerNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\ValueObject\ManufacturerId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class ManufacturerRepository extends AbstractObjectModelRepository
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @param ManufacturerId $manufacturerId
     *
     * @throws ManufacturerNotFoundException
     */
    public function assertManufacturerExists(ManufacturerId $manufacturerId): void
    {
        $this->assertObjectModelExists(
            $manufacturerId->getValue(),
            'manufacturer',
            ManufacturerNotFoundException::class
        );
    }

    /**
     * Exact-name lookup (legacy Manufacturer::getIdByName parity).
     *
     * manufacturer.name carries no unique constraint, so EVERY match is returned,
     * ordered by id ASC: callers use the first one and report the count when there
     * is more than one, rather than silently picking the oldest homonym.
     *
     * @return list<int>
     */
    public function getManufacturerIdsByName(string $name): array
    {
        $manufacturerIds = $this->connection->createQueryBuilder()
            ->select('m.id_manufacturer')
            ->from($this->dbPrefix . 'manufacturer', 'm')
            ->where('m.name = :name')
            ->orderBy('m.id_manufacturer', 'ASC')
            ->setParameter('name', $name)
            ->executeQuery()
            ->fetchFirstColumn()
        ;

        return array_map('intval', $manufacturerIds);
    }
}
