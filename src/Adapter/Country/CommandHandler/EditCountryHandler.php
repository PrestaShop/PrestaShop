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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\EditCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\EditCountryHandlerInterface;

/**
 * Handles update of country and address format
 */
#[AsCommandHandler]
class EditCountryHandler implements EditCountryHandlerInterface
{
    /**
     * @var CountryRepository
     */
    private $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(EditCountryCommand $command): void
    {
        $country = $this->countryRepository->get($command->getCountryId());

        if (null !== $command->getLocalizedNames()) {
            $country->name = $command->getLocalizedNames();
        }
        if (null !== $command->getIsoCode()) {
            $country->iso_code = $command->getIsoCode();
        }

        if (null !== $command->getCallPrefix()) {
            $country->call_prefix = $command->getCallPrefix();
        }

        if (null !== $command->needZipCode()) {
            $country->need_zip_code = $command->needZipCode();
        }

        if (null !== $command->isEnabled()) {
            $country->active = $command->isEnabled();
        }

        if (null !== $command->needIdNumber()) {
            $country->need_identification_number = $command->needIdNumber();
        }

        if (null !== $command->displayTaxLabel()) {
            $country->display_tax_label = $command->displayTaxLabel();
        }

        if (null !== $command->getShopAssociation()) {
            $country->id_shop_list = $command->getShopAssociation();
        }

        if (null !== $command->containsStates()) {
            $country->contains_states = $command->containsStates();
        }

        if (null !== $command->getZipCodeFormat()) {
            $country->zip_code_format = $command->getZipCodeFormat()->getValue();
        }

        if (null !== $command->getDefaultCurrency()) {
            $country->id_currency = $command->getDefaultCurrency();
        }

        if (null !== $command->getZoneId()) {
            $country->id_zone = $command->getZoneId();
        }

        $this->countryRepository->update($country);
    }
}
