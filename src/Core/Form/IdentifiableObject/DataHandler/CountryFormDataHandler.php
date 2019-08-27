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

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\EditCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Handles submitted country form data
 */
final class CountryFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc]
     */
    public function create(array $data)
    {
        $addCountryCommand = new AddCountryCommand(
            $data['country'],
            $data['iso_code'],
            (int) $data['call_prefix'],
            $data['address_format']
        );

        if (null !== $data['zip_code_format']) {
            $addCountryCommand->setZipCodeFormat($data['zip_code_format']);
        }

        if (null !== $data['default_currency']) {
            $addCountryCommand->setDefaultCurrency($data['default_currency']);
        }

        if (null !== $data['zone'] && !empty($data['zone'])) {
            $addCountryCommand->setZone($data['zone']);
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

        if (null !== $data['shop_association'] && !empty($data['shop_association'])) {
            $addCountryCommand->setShopAssociation($data['shop_association']);
        }

        /** @var CountryId $countryId */
        $countryId = $this->commandBus->handle($addCountryCommand);

        return $countryId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryConstraintException
     */
    public function update($countryId, array $data)
    {
        /** @var CountryId $countryIdObject */
        $countryIdObject = new CountryId((int) $countryId);
        $command = new EditCountryCommand(
            $countryIdObject,
            $data['country'],
            $data['iso_code'],
            (int) $data['call_prefix'],
            $data['zone'],
            $data['need_zip_code'],
            $data['address_format'],
            $data['is_enabled'],
            $data['contains_states'],
            $data['need_identification_number'],
            $data['display_tax_label']
        );

        if (null !== $data['default_currency']) {
            $command->setDefaultCurrency($data['default_currency']);
        }

        if (null !== $data['zip_code_format']) {
            $command->setZipCodeFormat($data['zip_code_format']);
        }

        if (null !== $data['shop_association']) {
            $command->setShopAssociation($data['shop_association']);
        }

        $this->commandBus->handle($command);
    }
}
