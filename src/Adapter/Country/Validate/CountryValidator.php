<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\Validate;

use Country;
use PrestaShop\PrestaShop\Adapter\AbstractObjectModelValidator;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotDeleteCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\DuplicateCountryIsoCodeException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Validates Country properties using legacy object model
 */
class CountryValidator extends AbstractObjectModelValidator
{
    public function __construct(
        private readonly ShopConfigurationInterface $configuration,
        private readonly ShopRepository $shopRepository,
    ) {
    }

    public function validate(Country $country)
    {
        if (!$country->validateFields(false) || !$country->validateFieldsLang(false)) {
            throw new CountryConstraintException('Country contains invalid field values');
        }

        $this->assertIsoCodeIsUnique($country);
    }

    /**
     * A deletion removes the country from every shop: it must not be the default country of any of them, nor the
     * all shops value that a shop without its own value inherits.
     *
     * @throws CannotDeleteCountryException
     */
    public function assertIsNotDefaultCountry(CountryId $countryId): void
    {
        $shopConstraints = array_map(
            fn (int $shopId): ShopConstraint => ShopConstraint::shop($shopId),
            $this->shopRepository->getAllShopIds()
        );
        $shopConstraints[] = ShopConstraint::allShops();

        foreach ($shopConstraints as $shopConstraint) {
            if ($countryId->getValue() === (int) $this->configuration->get('PS_COUNTRY_DEFAULT', null, $shopConstraint)) {
                throw new CannotDeleteCountryException(
                    sprintf('The default country #%d cannot be deleted', $countryId->getValue()),
                    CannotDeleteCountryException::DEFAULT_COUNTRY
                );
            }
        }
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
