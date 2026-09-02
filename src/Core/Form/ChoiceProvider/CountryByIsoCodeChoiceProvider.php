<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CountryChoiceProvider is responsible for providing both enabled/disabled country choices with ISO code values.
 */
final class CountryByIsoCodeChoiceProvider implements FormChoiceProviderInterface
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
     * @param int $langId
     * @param CountryDataProvider $countryDataProvider
     */
    public function __construct(
        $langId,
        CountryDataProvider $countryDataProvider
    ) {
        $this->countryDataProvider = $countryDataProvider;
        $this->langId = $langId;
    }

    /**
     * Get country choices.
     *
     * @return array
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            $this->countryDataProvider->getCountries($this->langId),
            'iso_code',
            'name'
        );
    }
}
