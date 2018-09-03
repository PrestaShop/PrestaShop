<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use PDO;

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
            ->setParameter('id_shop', $shopId)
        ;

        return $qb->execute()->fetchAll(PDO::FETCH_COLUMN);
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
            ->setParameter('id_shop', $shopId)
        ;

        return $qb->execute()->fetchAll(PDO::FETCH_COLUMN);
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
            ->setParameter('id_shop', $shopId)
        ;

        return $qb->execute()->fetchAll(PDO::FETCH_COLUMN);
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
            ->setParameter('id_shop', $shopId)
        ;

        return $qb->execute()->fetchAll(PDO::FETCH_COLUMN);
    }
}
