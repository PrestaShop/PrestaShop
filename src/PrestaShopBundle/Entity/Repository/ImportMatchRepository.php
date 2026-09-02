<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

/**
 * Class ImportMatchRepository retrieves import matches data from the database.
 */
class ImportMatchRepository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string database table name with prefix
     */
    private $importMatchTable;

    /**
     * @param Connection $connection
     * @param string $tablePrefix
     */
    public function __construct(Connection $connection, $tablePrefix)
    {
        $this->connection = $connection;
        $this->importMatchTable = $tablePrefix . 'import_match';
    }

    /**
     * Find one item by ID.
     *
     * @param int $id
     *
     * @return array
     */
    public function findOneById($id)
    {
        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($this->importMatchTable)
            ->where('id_import_match = :id')
            ->setParameter('id', $id);

        return $queryBuilder->executeQuery()->fetchAssociative();
    }

    /**
     * Find one item by name.
     *
     * @param string $name
     *
     * @return array
     */
    public function findOneByName($name)
    {
        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($this->importMatchTable)
            ->where('`name` = :name')
            ->setParameter('name', $name);

        return $queryBuilder->executeQuery()->fetchAssociative();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        $queryBuilder = $this->connection
            ->createQueryBuilder()
            ->select('*')
            ->from($this->importMatchTable);

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * Delete one import match by it's id.
     *
     * @param int $id
     */
    public function deleteById($id)
    {
        $this->connection->delete(
            $this->importMatchTable,
            [
                'id_import_match' => $id,
            ]
        );
    }
}
