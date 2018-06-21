<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Localization;

use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class CountryChoiceProvider is responsible for providing country choices for ChoiceType form field
 */
class CountryChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    /**
     * @param LegacyContext $legacyContext
     * @param CountryDataProvider $countryDataProvider
     */
    public function __construct(
        LegacyContext $legacyContext,
        CountryDataProvider $countryDataProvider
    ) {
        $this->legacyContext = $legacyContext;
        $this->countryDataProvider = $countryDataProvider;
    }

    /**
     * Get currency choices
     *
     * @return array
     */
    public function getChoices()
    {
        $contextLanguage = $this->legacyContext->getLanguage();
        $countries = $this->countryDataProvider->getCountries($contextLanguage->id);
        $choices = [];

        foreach ($countries as $country) {
            $choices[$country['name']] = $country['id_country'];
        }

        return $choices;
    }
}
