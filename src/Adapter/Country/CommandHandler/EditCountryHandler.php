<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use AddressFormat;
use Cache;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\AddressFormat\AddressFormatCheckerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\EditCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\EditCountryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CannotEditCountryException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\InvalidAddressFormatException;

/**
 * Handles update of country and address format
 */
#[AsCommandHandler]
class EditCountryHandler implements EditCountryHandlerInterface
{
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly AddressFormatCheckerInterface $addressFormatChecker,
    ) {
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

        $addressFormat = $command->getAddressFormat();
        if (null !== $addressFormat) {
            $errors = $this->addressFormatChecker->validate($addressFormat);
            if ([] !== $errors) {
                throw new InvalidAddressFormatException(
                    $errors,
                    'Invalid address format',
                    CountryConstraintException::INVALID_ADDRESS_FORMAT
                );
            }
        }

        $this->countryRepository->update($country);

        if (null !== $addressFormat) {
            $this->saveAddressFormat($command->getCountryId()->getValue(), $addressFormat);
        }
    }

    /**
     * @throws CannotEditCountryException when the address format row cannot be persisted
     */
    private function saveAddressFormat(int $countryId, string $format): void
    {
        $addressFormatModel = new AddressFormat($countryId);
        if (null === $addressFormatModel->id_country || 0 === (int) $addressFormatModel->id_country) {
            $addressFormatModel->id_country = $countryId;
        }
        $addressFormatModel->format = $format;

        if (!$addressFormatModel->save()) {
            throw new CannotEditCountryException(sprintf('Failed to save address format for country %d', $countryId));
        }

        // The legacy AddressFormat::getFormatDB caches per-country reads in a static
        // process cache that save() does not invalidate. Clear it so subsequent reads
        // (e.g. GetCountryForEditing immediately after this command) return the new value.
        Cache::clean('AddressFormat::getFormatDB' . $countryId);
    }
}
