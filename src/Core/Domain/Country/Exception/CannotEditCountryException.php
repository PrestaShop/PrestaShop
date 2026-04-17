<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Exception;

/**
 * Is thrown when adding new country fails
 */
class CannotEditCountryException extends CountryException
{
    public const FAILED_TO_UPDATE_COUNTRY = 10;

    public const UNKNOWN_EXCEPTION = 20;
}
