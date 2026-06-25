<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

/**
 * Defines the SQL index strategy that can be applied to an extra property storage column.
 *
 * The string value is stored in the extra_property_definition registry and used by
 * ExtraPropertySchemaManager when creating or synchronising the index on the *_extra table column.
 */
enum ExtraPropertySqlIndex: string
{
    /** No index on this column. */
    case NONE = 'none';

    /** Standard (non-unique) index on this column. */
    case KEY = 'key';

    /** Unique index on this column; enforces uniqueness at DB level. */
    case UNIQUE = 'unique';
}
