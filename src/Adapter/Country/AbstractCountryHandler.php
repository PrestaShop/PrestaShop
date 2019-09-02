<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Country;

use AddressFormat;
use Country;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;
use PrestaShopException;

/**
 * Abstract country handler provides common methods for country handlers
 */
abstract class AbstractCountryHandler
{
    /**
     * Get legacy country
     *
     * @param CountryId $countryId
     *
     * @return Country
     *
     * @throws CountryNotFoundException
     */
    protected function getCountry(CountryId $countryId): Country
    {
        $countryIdValue = $countryId->getValue();

        try {
            $country = new Country($countryIdValue);
        } catch (PrestaShopException $e) {
            throw new CountryNotFoundException(
                sprintf('Country with id "%s" was not found.', $countryIdValue)
            );
        }

        if ($country->id !== $countryIdValue) {
            throw new CountryNotFoundException(
                sprintf('Country with id "%s" was not found.', $countryIdValue)
            );
        }

        return $country;
    }

    /**
     * @param int $countryId
     * @param string $format
     *
     * @return AddressFormat
     *
     * @throws AddressConstraintException
     */
    protected function getValidAddressFormat(int $countryId, string $format): AddressFormat
    {
        try {
            $addressFormat = new AddressFormat($countryId);

            $addressFormat->format = $format;
            $addressFormat->id_country = $addressFormat->id_country ?? $countryId;

            $isInvalidAddressFormat = !$addressFormat->checkFormatFields() ||
                strlen($addressFormat->format) <= 0 ||
                !$addressFormat->validateFields(false);
        } catch (PrestaShopException $e) {
            throw new AddressConstraintException(
                sprintf('Address format: "%s" is invalid', $format),
                AddressConstraintException::INVALID_FORMAT
            );
        }

        if ($isInvalidAddressFormat) {
            throw new AddressConstraintException(
                sprintf('Address format: "%s" is invalid', $addressFormat->format),
                AddressConstraintException::INVALID_FORMAT
            );
        }

        return $addressFormat;
    }

    /**
     * @param Country $country
     *
     * @throws CountryConstraintException
     * @throws PrestaShopException
     */
    protected function validateCountryFields(Country $country)
    {
        if (!$country->validateFields(false) || !$country->validateFieldsLang(false)) {
            throw new CountryConstraintException(
                'Country contains invalid field values'
            );
        }
    }
}
