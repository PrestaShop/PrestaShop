<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\Validate;

use Country;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;
use State;
use Validate;

final class StoreValidator
{
    /**
     * Ensures the selected country exists and the selected state is consistent with the selected country:
     * a country containing states requires one belonging to it, and a country without states must not have one.
     *
     * @throws StoreConstraintException
     */
    public function assertStateCountryConsistency(int $countryId, ?int $stateId): void
    {
        $country = new Country($countryId);
        if (!Validate::isLoadedObject($country)) {
            throw new StoreConstraintException(
                sprintf('Country with id "%d" does not exist.', $countryId),
                StoreConstraintException::INVALID_COUNTRY
            );
        }

        if ($country->contains_states && !$stateId) {
            throw new StoreConstraintException(
                'A state is required for the selected country.',
                StoreConstraintException::INVALID_STATE
            );
        }

        if (!$country->contains_states && $stateId) {
            throw new StoreConstraintException(
                'The selected country does not contain states.',
                StoreConstraintException::STATE_COUNTRY_MISMATCH
            );
        }

        if ($stateId && (int) (new State($stateId))->id_country !== $countryId) {
            throw new StoreConstraintException(
                sprintf('State with id "%d" does not belong to country with id "%d".', $stateId, $countryId),
                StoreConstraintException::STATE_NOT_IN_COUNTRY
            );
        }
    }

    /**
     * Ensures the coordinates, once stored with 8 decimals, are within the geographic bounds: -90 to 90 for the
     * latitude, -180 to 180 for the longitude. A null coordinate is not checked (absent from the command, or no
     * coordinate).
     *
     * @throws StoreConstraintException
     */
    public function assertCoordinates(?DecimalNumber $latitude, ?DecimalNumber $longitude): void
    {
        $this->assertCoordinate($latitude, 90, 'Latitude', StoreConstraintException::INVALID_LATITUDE);
        $this->assertCoordinate($longitude, 180, 'Longitude', StoreConstraintException::INVALID_LONGITUDE);
    }

    /**
     * @throws StoreConstraintException
     */
    private function assertCoordinate(?DecimalNumber $coordinate, int $bound, string $name, int $errorCode): void
    {
        if (null === $coordinate) {
            return;
        }

        if (!Validate::isCoordinate($coordinate->toPrecision(8), $bound)) {
            throw new StoreConstraintException(
                sprintf('%s "%s" is invalid, it must be between -%d and %d.', $name, $coordinate, $bound, $bound),
                $errorCode
            );
        }
    }
}
