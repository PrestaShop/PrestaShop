<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;

/**
 * Class DatabaseTablesList stores list of database tables.
 */
class DatabaseTablesList
{
    /**
     * @var string[]
     */
    private $dbTables;

    /**
     * @param string[] $dbTables
     *
     * @throws SqlRequestException
     */
    public function __construct(array $dbTables)
    {
        $this->setTables($dbTables);
    }

    /**
     * @return string[]
     */
    public function getTables()
    {
        return $this->dbTables;
    }

    /**
     * @param array $tables
     *
     * @return self
     *
     * @throws SqlRequestException
     */
    private function setTables(array $tables)
    {
        $filteredTables = array_filter($tables, 'is_string');

        if ($filteredTables !== $tables) {
            throw new SqlRequestException('Invalid database table list provided. Database tables list must contain string values only.');
        }

        $this->dbTables = $tables;

        return $this;
    }
}
