<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement;

/**
 * Class SqlRequestExecutionResult stores result of SqlRequest SQL query execution result.
 */
class SqlRequestExecutionResult
{
    /**
     * @var array
     */
    private $columns;

    /**
     * @var array
     */
    private $rows;

    /**
     * @param array $columns
     * @param array $rows
     */
    public function __construct(array $columns, array $rows)
    {
        $this->columns = $columns;
        $this->rows = $rows;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return array
     */
    public function getRows()
    {
        return $this->rows;
    }
}
