<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
