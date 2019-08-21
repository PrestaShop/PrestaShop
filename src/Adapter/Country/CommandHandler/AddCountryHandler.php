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

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use AddressFormat;
use Country;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotAddAddressException;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\AddCountryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotAddCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShopException;

/**
 * Handles creation of country
 */
class AddCountryHandler implements AddCountryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AddressConstraintException
     * @throws CannotAddAddressException
     * @throws CannotAddCountryException
     * @throws CountryConstraintException
     * @throws CountryException
     */
    public function handle(AddCountryCommand $command)
    {
        try {
            $country = new Country();

            $country->name = $command->getLocalisedNames();
            $country->iso_code = $command->getIsoCode();
            $country->call_prefix = $command->getCallPrefix();
            $country->need_zip_code = $command->needZipCode();
            $country->zip_code_format = $command->getZipCodeFormat();
            $country->active = $command->isEnabled();
            $country->need_identification_number = $command->needIdNumber();
            $country->display_tax_label = $command->displayTaxLabel();
            $country->id_shop_list = $command->getShopAssociation();
            $country->contains_states = $command->containsStates();

            if (null !== $command->getDefaultCurrency()) {
                $country->id_currency = $command->getDefaultCurrency();
            }

            if (null !== $command->getZone()) {
                $country->id_zone = $command->getZone();
            }

            if (!$country->validateFields(false) || !$country->validateFieldsLang(false)) {
                throw new CountryConstraintException(
                    'Country contains invalid field values',
                    CountryConstraintException::INVALID_FIELDS
                );
            }

            $addressFormat = new AddressFormat($country->id);
            $addressFormat->format = $command->getAddressFormat();

            if (!$addressFormat->checkFormatFields() || strlen($addressFormat->format) <= 0 || !$addressFormat->validateFields(false)) {
                throw new AddressConstraintException(
                    sprintf('Address format: "%s" is invalid', $addressFormat->format),
                    AddressConstraintException::INVALID_FORMAT
                );
            }

            if (false === $country->add()) {
                throw new CannotAddCountryException(
                    'Failed to add country'
                );
            }

            if (false === $addressFormat->add()) {
                throw new CannotAddAddressException(
                    'Failed to add address format',
                    CannotAddAddressException::ADDRESS_FORMAT
                );
            }
        } catch (PrestaShopException $e) {
            throw new CountryException('An unexpected error occurred when adding country', 0, $e);
        }
    }
}
