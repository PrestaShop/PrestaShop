<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use AddressFormat;
use Cache;
use Country;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat\AddressFormatCheckerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\AddCountryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotAddCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\InvalidAddressFormatException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Handles creation of country and address format
 */
#[AsCommandHandler]
class AddCountryHandler implements AddCountryHandlerInterface
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly AddressFormatCheckerInterface $addressFormatChecker,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AddCountryCommand $command): CountryId
    {
        $errors = $this->addressFormatChecker->validate($command->getAddressFormat());
        if ([] !== $errors) {
            throw new InvalidAddressFormatException(
                $errors,
                'Invalid address format',
                CountryConstraintException::INVALID_ADDRESS_FORMAT
            );
        }

        $country = new Country();

        $country->name = $command->getLocalizedNames();
        $country->iso_code = $command->getIsoCode();
        $country->call_prefix = $command->getCallPrefix();
        $country->need_zip_code = $command->needZipCode();
        $country->active = $command->isEnabled();
        $country->need_identification_number = $command->needIdNumber();
        $country->display_tax_label = $command->displayTaxLabel();
        $country->id_shop_list = $command->getShopAssociation();
        $country->contains_states = $command->containsStates();

        if (null !== $command->getZipCodeFormat()) {
            $country->zip_code_format = $command->getZipCodeFormat()->getValue();
        }

        if (null !== $command->getDefaultCurrency()) {
            $country->id_currency = $command->getDefaultCurrency();
        }

        if (null !== $command->getZoneId()) {
            $country->id_zone = $command->getZoneId()->getValue();
        }

        $this->countryRepository->add($country);

        $countryId = (int) $country->id;
        $this->saveAddressFormat($countryId, $command->getAddressFormat());

        return new CountryId($countryId);
    }

    /**
     * @throws CannotAddCountryException when the address format row cannot be persisted
     */
    private function saveAddressFormat(int $countryId, string $format): void
    {
        $addressFormatModel = new AddressFormat();
        $addressFormatModel->id_country = $countryId;
        $addressFormatModel->format = $format;

        if (!$addressFormatModel->save()) {
            throw new CannotAddCountryException(sprintf('Failed to save address format for country %d', $countryId));
        }

        // The legacy AddressFormat::getFormatDB caches per-country reads in a static
        // process cache that save() does not invalidate. Clear it so subsequent reads
        // (e.g. GetCountryForEditing immediately after this command) return the new value.
        Cache::clean('AddressFormat::getFormatDB' . $countryId);
    }
}
