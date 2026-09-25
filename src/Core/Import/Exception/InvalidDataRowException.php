<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Import\Exception;

/**
 * Class InvalidDataRowException thrown when import handler encounters an invalid data row.
 *
 * @deprecated since 9.3, will be removed in the next major version - replaced by the import engine, which reports an invalid row as an ImportMessage
 */
class InvalidDataRowException extends ImportException
{
}
