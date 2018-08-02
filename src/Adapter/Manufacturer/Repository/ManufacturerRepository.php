<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\Repository;

use Doctrine\DBAL\Connection;
use Manufacturer as LegacyManufacturer;
use PrestaShop\PrestaShop\Core\Manufacturer\ManufacturerInterface;
use PrestaShop\PrestaShop\Core\Manufacturer\ManufacturerRepositoryInterface;

/**
 * Creates a new Manufacturer.
 */
class ManufacturerRepository implements ManufacturerRepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $tablePrefix;

    /**
     * ManufacturerRepository constructor.
     * @param Connection $connection
     * @param $tablePrefix
     */
    public function __construct(Connection $connection, $tablePrefix)
    {
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;
    }

    public function create(ManufacturerInterface $manufacturer)
    {
        $legacyManufacturer = new LegacyManufacturer($manufacturer->getId(), $manufacturer->getIdLang());
        $legacyManufacturer->save();
    }

    /**
     * @param $id the Manufacturer id
     *
     * @return array
     */
    public function retrieveFromId($id)
    {
        $table = $this->tablePrefix . 'manufacturer';
        $sql = "SELECT * from $table WHERE id_manufacturer = ?";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindValue(1, $id);

        return $stmt->execute();
    }
}
