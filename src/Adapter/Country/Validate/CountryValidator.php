<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\Validate;

use Country;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\DuplicateCountryIsoCodeException;

/**
 * Validates Country properties using legacy object model
 */
class CountryValidator extends AbstractObjectModelValidator
{
    public function validate(Country $country)
    {
        if (!$country->validateFields(false) || !$country->validateFieldsLang(false)) {
            throw new CountryConstraintException('Country contains invalid field values');
        }

        $this->assertIsoCodeIsUnique($country);
    }

    /**
     * Ensures no other country already uses the same ISO code, replicating the legacy
     * AdminCountriesController uniqueness check so the migrated page behaves identically.
     *
     * The ISO code format and presence are already guaranteed by the field validation
     * above (iso_code is required and validated as a language ISO code in the Country
     * ObjectModel definition), so Country::getByIso() never receives an invalid value.
     *
     * @throws DuplicateCountryIsoCodeException
     */
    private function assertIsoCodeIsUnique(Country $country): void
    {
        $existingCountryId = (int) Country::getByIso($country->iso_code);

        if (0 !== $existingCountryId && $existingCountryId !== (int) $country->id) {
            throw new DuplicateCountryIsoCodeException(sprintf(
                'Country with ISO code "%s" already exists',
                $country->iso_code
            ));
        }
    }
}
