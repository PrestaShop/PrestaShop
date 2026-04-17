<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\CommandHandler;

use Country;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Command\AddCountryCommand;
use PrestaShop\PrestaShop\Core\Domain\Country\CommandHandler\AddCountryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

/**
 * Handles creation of country and address format
 */
#[AsCommandHandler]
class AddCountryHandler implements AddCountryHandlerInterface
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
    public function handle(AddCountryCommand $command): CountryId
    {
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

        return new CountryId((int) $country->id);
    }
}
