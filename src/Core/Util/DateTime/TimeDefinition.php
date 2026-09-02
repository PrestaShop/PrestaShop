<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Util\DateTime;

/**
 * Class TimeDefinition defines common time intervals in different formats.
 */
final class TimeDefinition
{
    public const HOUR_IN_SECONDS = 3600;
    public const DAY_IN_SECONDS = 86400;

    /**
     * Object is not suppose to be initialized as it's responsibility is to define time constants.
     */
    private function __construct()
    {
    }
}
