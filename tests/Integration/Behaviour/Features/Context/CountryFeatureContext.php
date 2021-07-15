<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
        /** @var Country $country */
        $countryId = Country::getByIso($countryIsoCode);

        $country = new Country($countryId);
        $country->active = 1;
        $country->save();
    }

    /**
     * @Given country :countryIsoCode is disabled
     */
    public function disableCountry($countryIsoCode)
    {
        $this->checkCountryWithIsoCodeExists($countryIsoCode);
        /** @var Country $country */
        $countryId = Country::getByIso($countryIsoCode);

        $country = new Country($countryId);
        $country->active = 0;
        $country->save();
    }

    /**
     * @param $countryIsoCode
     *
     * @throws \RuntimeException
     */
    public function checkCountryWithIsoCodeExists($countryIsoCode)
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
