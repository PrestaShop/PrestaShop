<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\EditCountryCommand;
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
            (int) $data['default_currency'],
            (int) $data['zone'],
            $data['need_zip_code'],
            $data['zip_code_format'],
            (string) $data['address_format'],
            $data['is_enabled'],
            $data['contains_states'],
            $data['need_identification_number'],
            $data['display_tax_label'],
            $data['shop_association'] ?? []
        );

        /** @var CountryId $countryId */
        $countryId = $this->commandBus->handle($addCountryCommand);

        return $countryId->getValue();
    }

    public function update($id, array $data): void
    {
        $editCountryCommand = new EditCountryCommand($id);

        if (null !== $data['name']) {
            $editCountryCommand->setLocalizedNames($data['name']);
        }

        if (null !== $data['iso_code']) {
            $editCountryCommand->setIsoCode($data['iso_code']);
        }

        if (null !== $data['call_prefix']) {
            $editCountryCommand->setCallPrefix((int) $data['call_prefix']);
        }

        if (null !== $data['address_format']) {
            $editCountryCommand->setAddressFormat($data['address_format']);
        }

        if (null !== $data['zip_code_format']) {
            $editCountryCommand->setZipCodeFormat($data['zip_code_format']);
        }

        if (null !== $data['default_currency']) {
            $editCountryCommand->setDefaultCurrency($data['default_currency']);
        }

        if (null !== $data['zone']) {
            $editCountryCommand->setZoneId($data['zone']);
        }

        if (null !== $data['need_zip_code']) {
            $editCountryCommand->setNeedZipCode($data['need_zip_code']);
        }

        if (null !== $data['is_enabled']) {
            $editCountryCommand->setEnabled($data['is_enabled']);
        }

        if (null !== $data['contains_states']) {
            $editCountryCommand->setContainsStates($data['contains_states']);
        }

        if (null !== $data['need_identification_number']) {
            $editCountryCommand->setNeedIdNumber($data['need_identification_number']);
        }

        if (null !== $data['display_tax_label']) {
            $editCountryCommand->setDisplayTaxLabel($data['display_tax_label']);
        }

        if (isset($data['shop_association'])) {
            $editCountryCommand->setShopAssociation($data['shop_association']);
        }

        $this->commandBus->handle($editCountryCommand);
    }
}
