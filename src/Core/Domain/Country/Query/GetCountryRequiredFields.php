<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\Query;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Query for getting country required fields
 */
class GetCountryRequiredFields
{
    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @param int $countryId
     *
     * @throws CountryConstraintException
     */
    public function __construct(int $countryId)
    {
        $this->countryId = new CountryId($countryId);
    }

    /**
     * @return CountryId
     */
    public function getCountryId(): CountryId
    {
        return $this->countryId;
    }
}
