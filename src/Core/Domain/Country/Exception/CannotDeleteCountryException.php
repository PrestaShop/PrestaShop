<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Exception;

/**
 * Is thrown when a country cannot be deleted
 */
class CannotDeleteCountryException extends CountryException
{
    /**
     * The default country of the shop cannot be deleted
     */
    public const DEFAULT_COUNTRY = 1;
}
