<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Country\Command;

use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Toggles countries status on bulk action.
 */
final class BulkToggleCountriesStatusCommand
{
    private bool $expectedStatus;

    /** @var array<int, CountryId> */
    private array $countryIds = [];

    /**
     * @param array<int, int> $countryIds
     */
    public function __construct(bool $expectedStatus, array $countryIds)
    {
        $this->setCountryIds($countryIds);
        $this->expectedStatus = $expectedStatus;
    }

    public function getExpectedStatus(): bool
    {
        return $this->expectedStatus;
    }

    /**
     * @return array<int, CountryId>
     */
    public function getCountryIds(): array
    {
        return $this->countryIds;
    }

    /**
     * @param array<int, int> $countryIds
     */
    private function setCountryIds(array $countryIds): void
    {
        foreach ($countryIds as $countryId) {
            $this->countryIds[] = new CountryId((int) $countryId);
        }
    }
}
