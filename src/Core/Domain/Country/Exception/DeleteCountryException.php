<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\Exception;

/**
 * Is thrown on failure to delete country
 */
class DeleteCountryException extends CountryException
{
    /**
     * When fails to delete single country
     */
    public const FAILED_DELETE = 1;

    /**
     * When fails to delete countries in bulk actions
     */
    public const FAILED_BULK_DELETE = 2;
}
