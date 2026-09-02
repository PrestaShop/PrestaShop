<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;

/**
 * Class ModuleRepository is responsible for retrieving module data from database.
 */
class ModuleRepository
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $databasePrefix;

    /**
     * @var string
     */
    private $table;

    /**
     * @param Connection $connection
     * @param string $databasePrefix
     */
    public function __construct(Connection $connection, $databasePrefix)
    {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
        $this->table = $this->databasePrefix . 'module';
    }

    /**
     * Find enabled countries for module in shop.
     *
     * @param int $moduleId
     * @param int $shopId
     *
     * @return int[] Array of country IDs
     */
    public function findRestrictedCountryIds($moduleId, $shopId)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('mc.id_country')
            ->from($this->table . '_country', 'mc')
            ->where('mc.id_module = :id_module')
            ->setParameter('id_module', $moduleId)
            ->andWhere('mc.id_shop = :id_shop')
            ->setParameter('id_shop', $shopId);

        return $qb->executeQuery()->fetchFirstColumn();
    }

    /**
     * Find enabled currencies for module in shop.
     *
     * @param int $moduleId
     * @param int $shopId
     *
     * @return int[] Array of currency IDs
     */
    public function findRestrictedCurrencyIds($moduleId, $shopId)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('mc.id_currency')
            ->from($this->table . '_currency', 'mc')
            ->where('mc.id_module = :id_module')
            ->setParameter('id_module', $moduleId)
            ->andWhere('mc.id_shop = :id_shop')
            ->setParameter('id_shop', $shopId);

        return $qb->executeQuery()->fetchFirstColumn();
    }

    /**
     * Find enabled groups for module in shop.
     *
     * @param int $moduleId
     * @param int $shopId
     *
     * @return int[] Array of group IDs
     */
    public function findRestrictedGroupIds($moduleId, $shopId)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('mg.id_group')
            ->from($this->table . '_group', 'mg')
            ->where('mg.id_module = :id_module')
            ->setParameter('id_module', $moduleId)
            ->andWhere('mg.id_shop = :id_shop')
            ->setParameter('id_shop', $shopId);

        return $qb->executeQuery()->fetchFirstColumn();
    }

    /**
     * Find enabled carriers for module in shop.
     *
     * @param int $moduleId
     * @param int $shopId
     *
     * @return int[] Array of carrier references
     */
    public function findRestrictedCarrierReferenceIds($moduleId, $shopId)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('mc.id_reference')
            ->from($this->table . '_carrier', 'mc')
            ->where('mc.id_module = :id_module')
            ->setParameter('id_module', $moduleId)
            ->andWhere('mc.id_shop = :id_shop')
            ->setParameter('id_shop', $shopId);

        return $qb->executeQuery()->fetchFirstColumn();
    }
}
