<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CountryByIdChoiceProvider provides country choices with ID values.
 */
final class CountryByIdChoiceProvider implements FormChoiceProviderInterface, FormChoiceAttributeProviderInterface
{
    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    /**
     * @var int
     */
    private $langId;

    /**
     * @var array
     */
    private $countries;

    /**
     * @var int[]
     */
    private $dniCountriesId;

    /**
     * @var int[]
     */
    private $postcodeCountriesId;

    /**
     * @param int $langId
     * @param CountryDataProvider $countryDataProvider
     */
    public function __construct(
        $langId,
        CountryDataProvider $countryDataProvider,
        private string $psImageDir,
        private string $psImageBaseUrl
    ) {
        $this->langId = $langId;
        $this->countryDataProvider = $countryDataProvider;
    }

    /**
     * Get currency choices.
     *
     * @return array
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->getCountries(),
            'id_country',
            'name'
        );
    }

    /**
     * @return array
     */
    public function getChoicesAttributes()
    {
        $countries = $this->getCountries();
        $dniCountriesId = $this->getDniCountriesId();
        $postcodeCountriesId = $this->getPostcodeCountriesId();
        $choicesAttributes = [];

        foreach ($countries as $country) {
            if (in_array($country['id_country'], $dniCountriesId)) {
                $choicesAttributes[$country['name']]['need_dni'] = 1;
            }
            if (in_array($country['id_country'], $postcodeCountriesId)) {
                $choicesAttributes[$country['name']]['need_postcode'] = 1;
            }

            $flagPath = 'flags/' . strtolower($country['iso_code']) . '.jpg';

            if (file_exists($this->psImageDir . $flagPath)) {
                $choicesAttributes[$country['name']]['data-logo'] = $this->psImageBaseUrl . $flagPath;
            }
        }

        return $choicesAttributes;
    }

    /**
     * @return array
     */
    private function getCountries()
    {
        if (null === $this->countries) {
            $this->countries = $this->countryDataProvider->getCountries($this->langId);
        }

        return $this->countries;
    }

    /**
     * @return int[]
     */
    private function getDniCountriesId()
    {
        if (null === $this->dniCountriesId) {
            $this->dniCountriesId = $this->countryDataProvider->getCountriesIdWhichNeedDni();
        }

        return $this->dniCountriesId;
    }

    private function getPostcodeCountriesId()
    {
        if (null === $this->postcodeCountriesId) {
            $this->postcodeCountriesId = $this->countryDataProvider->getCountriesIdWhichNeedPostcode();
        }

        return $this->postcodeCountriesId;
    }
}
