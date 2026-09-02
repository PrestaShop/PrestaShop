<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Definition;

/**
 * Supported types for extra property fields.
 *
 * The label string is stored in extra_property_definition.type as a MySQL ENUM
 * (same literals as this backed enum). The physical column DDL on value tables is computed
 * at runtime by ColumnDefinitionMapper, so no SQL fragment is persisted in the registry row.
 */
enum ExtraPropertyType: string
{
    case INT = 'int';
    case BOOL = 'bool';
    case STRING = 'string';
    case FLOAT = 'float';
    case DATE = 'date';
    case HTML = 'html';
    case JSON = 'json';
    case CHOICE = 'choice';
}
