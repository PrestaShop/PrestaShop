<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception;

/**
 * Class SqlRequestSettingsConstraintException is thrown when invalid settings are supplied.
 */
class SqlRequestSettingsConstraintException extends SqlRequestException
{
    public const INVALID_FILE_ENCODING = 10;
    public const NOT_SUPPORTED_FILE_ENCODING = 20;
    public const INVALID_FILE_SEPARATOR = 30;
}
