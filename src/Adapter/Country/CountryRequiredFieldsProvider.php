<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Country;

use PrestaShop\PrestaShop\Core\Country\CountryRequiredFieldsProviderInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

class CountryRequiredFieldsProvider implements CountryRequiredFieldsProviderInterface
{
    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    public function __construct(CountryDataProvider $countryDataProvider)
    {
        $this->countryDataProvider = $countryDataProvider;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryNotFoundException
     */
    public function isStatesRequired(CountryId $countryId): bool
    {
        $countries = $this->countryDataProvider->getCountriesIdWhichNeedState();

        return in_array($countryId->getValue(), $countries);
    }

    /**
     * {@inheritdoc}
     *
     * @throws CountryNotFoundException
     */
    public function isDniRequired(CountryId $countryId): bool
    {
        $countries = $this->countryDataProvider->getCountriesIdWhichNeedDni();

        return in_array($countryId->getValue(), $countries);
    }
}
