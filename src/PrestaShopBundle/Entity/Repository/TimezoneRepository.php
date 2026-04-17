<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

/**
 * Class TimezoneRepository.
 */
class TimezoneRepository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $timezoneTable;

    /**
     * @param Connection $connection
     * @param string $tablePrefix
     */
    public function __construct(Connection $connection, $tablePrefix)
    {
        $this->connection = $connection;
        $this->timezoneTable = $tablePrefix . 'timezone';
    }

    /**
     * Final all timezones from database.
     *
     * @return array
     */
    public function findAll()
    {
        $statement = $this->connection->query("SELECT t.* FROM $this->timezoneTable t");

        return $statement->fetchAll();
    }
}
