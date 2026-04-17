<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Deletes country
 */
class DeleteCountryCommand
{
    /**
     * @var CountryId
     */
    private $countryId;

    /**
     * @param int $countryId
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
