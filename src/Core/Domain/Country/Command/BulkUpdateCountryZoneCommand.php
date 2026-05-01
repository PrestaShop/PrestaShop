<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;

/**
 * Updates zone for given countries.
 */
class BulkUpdateCountryZoneCommand
{
    /**
     * @var int[]
     */
    private $countryIds;

    /**
     * @var int
     */
    private $newZoneId;

    /**
     * @param int[] $countryIds
     */
    public function __construct(array $countryIds, int $newZoneId)
    {
        if ($newZoneId <= 0) {
            throw new CountryException(sprintf('Zone Id must be integer greater than 0, but %s given.', var_export($newZoneId, true)));
        }

        $this->newZoneId = $newZoneId;
        $this->setCountryIds($countryIds);
    }

    /**
     * @return int[]
     */
    public function getCountryIds(): array
    {
        return $this->countryIds;
    }

    public function getNewZoneId(): int
    {
        return $this->newZoneId;
    }

    /**
     * @param int[] $countryIds
     */
    private function setCountryIds(array $countryIds): void
    {
        if (empty($countryIds)) {
            throw new CountryException('You must select at least one country.');
        }

        foreach ($countryIds as $countryId) {
            if (!is_int($countryId) || $countryId <= 0) {
                throw new CountryException(sprintf('Invalid country ID: %s', var_export($countryId, true)));
            }
        }

        $this->countryIds = $countryIds;
    }
}
