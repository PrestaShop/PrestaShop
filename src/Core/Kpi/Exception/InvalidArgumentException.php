<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Kpi\Exception;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Will be thrown if Kpi factory arguments are invalid.
 */
final class InvalidArgumentException extends CoreException
{
    /**
     * @param mixed $kpi
     *
     * @return InvalidArgumentException
     */
    public static function invalidKpi($kpi)
    {
        $exceptionMessage = sprintf(
            'Kpi must be an instance of KpiInterface, got `%s`.',
            gettype($kpi)
        );

        return new self($exceptionMessage);
    }

    /**
     * @param mixed $identifier
     *
     * @return InvalidArgumentException
     */
    public static function invalidIdentifier($identifier)
    {
        $exceptionMessage = sprintf(
            'Identifier must be a string, got `%s`.',
            gettype($identifier)
        );

        return new self($exceptionMessage);
    }
}
