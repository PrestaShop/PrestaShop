<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Updates zone for given countries.
 */
final class BulkUpdateCountryZoneCommand
{
    /** @var array<int, CountryId> */
    private array $countryIds = [];

    private int $newZoneId;

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
     * @return array<int, CountryId>
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
            $this->countryIds[] = new CountryId((int) $countryId);
        }
    }
}
