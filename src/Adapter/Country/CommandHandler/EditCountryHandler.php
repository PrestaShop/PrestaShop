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

use Country;
use PrestaShop\PrestaShop\Adapter\Country\AbstractCountryHandler;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\AddressConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Address\Exception\CannotUpdateAddressFormatException;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\EditCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\EditCountryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotUpdateCountryException;
use PrestaShopException;

/**
 * Handles editing of country and country address format
 */
final class EditCountryHandler extends AbstractCountryHandler implements EditCountryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws AddressConstraintException
     * @throws CannotUpdateAddressFormatException
     * @throws CannotUpdateCountryException
     * @throws CountryConstraintException
     * @throws CountryException
     * @throws CountryNotFoundException
     */
    public function handle(EditCountryCommand $command): void
    {
        try {
            $country = $this->getCountry($command->getCountryId());

            $country->name = $command->getLocalisedNames();
            $country->iso_code = $command->getIsoCode();
            $country->call_prefix = $command->getCallPrefix();
            $country->need_zip_code = $command->needZipCode();
            $country->active = $command->isEnabled();
            $country->need_identification_number = $command->needIdNumber();
            $country->display_tax_label = $command->displayTaxLabel();
            $country->contains_states = $command->containsStates();
            $country->id_zone = $command->getZone();

            if (null !== $command->getDefaultCurrency()) {
                $country->id_currency = $command->getDefaultCurrency();
            }

            if (null !== $command->getZipCodeFormat()) {
                $country->zip_code_format = $command->getZipCodeFormat()->getValue();
            }

            if (null !== $command->getShopAssociation()) {
                $country->id_shop_list = $command->getShopAssociation();
            }

            $this->validateCountryFields($country);
            $addressFormat = $this->getValidAddressFormat((int) $country->id, $command->getAddressFormat());

            if (false === $country->update()) {
                throw new CannotUpdateCountryException(
                    'Failed to update country'
                );
            }

            if (false === $addressFormat->update()) {
                throw new CannotUpdateAddressFormatException(
                    'Failed to update address format'
                );
            }
        } catch (PrestaShopException $e) {
            throw new CountryException('An unexpected error occurred when updating country', 0, $e);
        }
    }
}
