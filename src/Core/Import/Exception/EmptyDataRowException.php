<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Exception;

/**
 * Class EmptyDataRowException thrown when the import handler finds an empty data row.
 *
 * @deprecated since 9.3, will be removed in the next major version - replaced by the import engine, which reports an empty row as an ImportMessage
 */
class EmptyDataRowException extends InvalidDataRowException
{
}
