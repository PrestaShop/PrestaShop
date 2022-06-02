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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Handles submitted zone form data.
 */
class CountryFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    protected $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * Create object from form data.
     *
     * @param array $data
     *
     * @return int
     */
    public function create(array $data): int
    {
        $addCountryCommand = new AddCountryCommand(
            $data['name'],
            (string) $data['iso_code'],
            (int) $data['call_prefix'],
            (string) $data['address_format']
        );

        if (null !== $data['zip_code_format']) {
            $addCountryCommand->setZipCodeFormat($data['zip_code_format']);
        }

        if (null !== $data['default_currency']) {
            $addCountryCommand->setDefaultCurrency($data['default_currency']);
        }

        if (null !== $data['zone']) {
            $addCountryCommand->setZoneId($data['zone']);
        }

        if (null !== $data['need_zip_code']) {
            $addCountryCommand->setNeedZipCode($data['need_zip_code']);
        }

        if (null !== $data['is_enabled']) {
            $addCountryCommand->setEnabled($data['is_enabled']);
        }

        if (null !== $data['contains_states']) {
            $addCountryCommand->setContainsStates($data['contains_states']);
        }

        if (null !== $data['need_identification_number']) {
            $addCountryCommand->setNeedIdNumber($data['need_identification_number']);
        }

        if (null !== $data['display_tax_label']) {
            $addCountryCommand->setDisplayTaxLabel($data['display_tax_label']);
        }

        if (isset($data['shop_association'])) {
            $addCountryCommand->setShopAssociation($data['shop_association']);
        }

        /** @var CountryId $countryId */
        $countryId = $this->commandBus->handle($addCountryCommand);

        return $countryId->getValue();
    }

    public function update($id, array $data)
    {
        // TODO: Implement update() method.
    }
}
