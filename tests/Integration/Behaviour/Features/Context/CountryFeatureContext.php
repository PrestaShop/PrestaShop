<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Country;
use RuntimeException;

class CountryFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given country :countryReference with iso code :isoCode exists
     *
     * @param string $countryReference
     * @param string $isoCode
     */
    public function assertCountryExists(string $countryReference, string $isoCode): void
    {
        $countryId = (int) Country::getByIso($isoCode);

        if (!$countryId) {
            throw new RuntimeException(sprintf('Country "%s" does not exist', $countryReference));
        }

        SharedStorage::getStorage()->set($countryReference, $countryId);
    }

    /**
     * @Given country :countryIsoCode is enabled
     */
    public function enableCountry($countryIsoCode)
    {
        $this->checkCountryWithIsoCodeExists($countryIsoCode);
        $countryId = Country::getByIso($countryIsoCode);

        $country = new Country($countryId);
        $country->active = true;
        $country->save();

        $this->getSharedStorage()->set($countryIsoCode, (int) $countryId);
    }

    /**
     * @Given country :countryIsoCode is disabled
     */
    public function disableCountry($countryIsoCode)
    {
        $this->checkCountryWithIsoCodeExists($countryIsoCode);
        $countryId = Country::getByIso($countryIsoCode);

        $country = new Country($countryId);
        $country->active = false;
        $country->save();
    }

    /**
     * @param string $countryIsoCode
     *
     * @throws RuntimeException
     */
    public function checkCountryWithIsoCodeExists(string $countryIsoCode)
    {
        $country = Country::getByIso($countryIsoCode);

        if (false === $country) {
            throw new RuntimeException(sprintf('No country with ISO Code "%s"', $countryIsoCode));
        }
    }

    /**
     * @param string $countryIsoCode
     *
     * @return int
     */
    public function getCountryWithIsoCode(string $countryIsoCode): int
    {
        $country = Country::getByIso($countryIsoCode);

        if (false === $country) {
            throw new RuntimeException(sprintf('No country with ISO Code "%s"', $countryIsoCode));
        }

        return $country;
    }
}
