<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Country\QueryHandler;

use Doctrine\Common\Collections\ArrayCollection;
use PrestaShop\PrestaShop\Adapter\Country\CountryDataProvider;
use PrestaShop\PrestaShop\Adapter\Country\ValueObject\Country;
use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountries;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler\GetCountriesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\Countries;

/**
 * Handles get countries query and returns ArrayCollection|Country[] where Country is value object
 */
final class GetCountriesHandler implements GetCountriesHandlerInterface
{
    /**
     * @var CountryDataProvider
     */
    private $countryDataProvider;

    /**
     * @param CountryDataProvider $countryDataProvider
     */
    public function __construct(CountryDataProvider $countryDataProvider)
    {
        $this->countryDataProvider = $countryDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetCountries $query): Countries
    {
        $countries = $this->countryDataProvider->getCountries(
            $query->getLangId(),
            $query->isActive(),
            $query->doesContainStates(),
            $query->doesIncludeStatesList()
        );
        $countriesVO = new ArrayCollection();
        foreach ($countries as $country) {
            $countryVO = new Country((int) $country['id_country'], $country['name']);
            $countriesVO->add($countryVO);
        }

        return new Countries($countriesVO);
    }
}
