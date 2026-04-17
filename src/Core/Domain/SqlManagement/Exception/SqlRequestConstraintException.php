<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception;

/**
 * Is thrown when SqlRequest constraints are violated
 */
class SqlRequestConstraintException extends SqlRequestException
{
    /**
     * When provided name is not valid
     */
    public const INVALID_NAME = 10;

    /**
     * When provided sql query is not valid
     */
    public const INVALID_SQL_QUERY = 20;

    /**
     * When provided sql query format is invalid
     */
    public const MALFORMED_SQL_QUERY = 30;

    /**
     * When empty data is provided for bulk action
     */
    public const MISSING_BULK_DATA = 40;
}
