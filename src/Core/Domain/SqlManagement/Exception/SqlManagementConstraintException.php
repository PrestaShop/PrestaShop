<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception;

/**
 * Is thrown when SqlManagement constraints are violated
 */
class SqlManagementConstraintException extends SqlManagementException
{
    /**
     * When database table name is invalid
     */
    public const INVALID_DATABASE_TABLE_NAME = 10;

    /**
     * When database table field is invalid
     */
    public const INVALID_DATABASE_TABLE_FIELD = 20;

    /**
     * When database table field name is invalid
     */
    public const INVALID_DATABASE_TABLE_FIELD_NAME = 30;

    /**
     * When database table field type is invalid
     */
    public const INVALID_DATABASE_TABLE_FIELD_TYPE = 40;
}
