<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Import;

use Doctrine\DBAL\Connection;

/**
 * Class DataMatchSaver saves data configuration match.
 * This class will be removed with CQRS introduction.
 */
final class DataMatchSaver
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(Connection $connection, $dbPrefix)
    {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * Save data match.
     *
     * @param string $name name of the match
     * @param array $value value of the match
     * @param int $skipRows number of rows to skip from the import file
     *
     * @return bool
     */
    public function save($name, array $value, $skipRows)
    {
        return (bool) $this->connection->insert(
            $this->dbPrefix . 'import_match',
            [
                '`name`' => pSQL($name),
                '`match`' => pSQL(implode('|', $value)),
                '`skip`' => (int) $skipRows,
            ]
        );
    }
}
