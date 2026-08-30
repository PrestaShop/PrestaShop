<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\QueryHandler;

use AddressFormat;
use PrestaShop\PrestaShop\Adapter\Country\Repository\CountryRepository;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsQueryHandler;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler\GetCountryForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryForEditing;

/**
 * Handles editable country query
 */
#[AsQueryHandler]
class GetCountryForEditingHandler implements GetCountryForEditingHandlerInterface
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
    public function handle(GetCountryForEditing $command): CountryForEditing
    {
        $countryId = $command->getCountryId();
        $country = $this->countryRepository->get($countryId);

        // Read the raw stored format directly via the ObjectModel so the merchant
        // edits exactly what's persisted. AddressFormat::getAddressCountryFormat
        // would auto-append \ndni for countries that need DNI — that's a display-time
        // decoration that should not leak into the edit form.
        $addressFormatModel = new AddressFormat($countryId->getValue());
        $storedFormat = (string) $addressFormatModel->format;

        return new CountryForEditing(
            $command->getCountryId(),
            $country->name,
            (string) $country->iso_code,
            (int) $country->call_prefix,
            (int) $country->id_currency,
            (int) $country->id_zone,
            (bool) $country->need_zip_code,
            (string) $country->zip_code_format,
            $storedFormat,
            (bool) $country->active,
            (bool) $country->contains_states,
            (bool) $country->need_identification_number,
            (bool) $country->display_tax_label,
            $country->getAssociatedShops()
        );
    }
}
