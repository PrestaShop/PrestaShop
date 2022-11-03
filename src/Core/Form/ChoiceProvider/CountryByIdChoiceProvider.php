<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
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
        CountryDataProvider $countryDataProvider
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
        $countries = $this->getCountries();
        $choices = [];

        foreach ($countries as $country) {
            $choices[$country['name']] = $country['id_country'];
        }

        return $choices;
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
