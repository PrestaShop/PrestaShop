<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Country;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

interface CountryRequiredFieldsProviderInterface
{
    /**
     * @param CountryId $countryId
     *
     * @return bool
     */
    public function isStatesRequired(CountryId $countryId): bool;

    /**
     * @param CountryId $countryId
     *
     * @return bool
     */
    public function isDniRequired(CountryId $countryId): bool;
}
