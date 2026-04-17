<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\Exception;

/**
 * Is thrown when country constraint is violated
 */
class CountryConstraintException extends CountryException
{
    public const INVALID_ID = 10;

    public const INVALID_ZIP_CODE = 20;
}
