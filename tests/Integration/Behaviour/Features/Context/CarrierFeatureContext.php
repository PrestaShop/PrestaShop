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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context;

use Address;
use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Carrier;
use CartRule;
use Configuration;
use Context;
use Country;
use Exception;
use RangePrice;
use RuntimeException;
use State;
use Zone;

class CarrierFeatureContext extends AbstractPrestaShopFeatureContext
{
    use CartAwareTrait;

    /**
     * @var Zone[]
     */
    protected $zones = [];

    /**
     * @var Country[]
     */
    protected $countries = [];

    /**
     * @var Country[]
     */
    protected $previousCountries = [];

    /**
     * @var State[]
     */
    protected $states = [];

    /**
     * @var Address[]
     */
    protected $addresses = [];

    /**
     * @var Carrier[]
     */
    protected $carriers = [];

    /**
     * @var RangePrice[]
     */
    protected $priceRanges = [];

    /**
     * @var CustomerFeatureContext
     */
    protected $customerFeatureContext;

    /** @BeforeScenario */
    public function before(BeforeScenarioScope $scope)
    {
        /** @var InitializedContextEnvironment $environment */
        $environment = $scope->getEnvironment();
        /** @var CustomerFeatureContext $customerFeatureContext */
        $customerFeatureContext = $environment->getContext(CustomerFeatureContext::class);

        $this->customerFeatureContext = $customerFeatureContext;
    }

    /**
     * @Given /^there is a country named "(.+)" and iso code "(.+)" in zone "(.+)"$/
     */
    public function createCountry($countryName, $isoCode, $zoneName)
    {
        $countryId = Country::getByIso($isoCode, false);
        if (!$countryId) {
            throw new Exception('Country not found with iso code = ' . $isoCode);
        }
        $country = new Country($countryId);
        // clone country to be able to properly reset previous data
        $this->previousCountries[$countryName] = clone $country;
        $this->countries[$countryName] = $country;
        $country->id_zone = $this->getSharedStorage()->get($zoneName)->id;
        $country->active = true;
        $country->save();

        $this->getSharedStorage()->set($countryName, (int) $countryId);
    }

    /**
     * @param string $countryName
     *
     * @return Country
     */
    public function getCountryWithName($countryName): Country
    {
        return $this->countries[$countryName];
    }

    /**
     * @param string $countryName
     */
    public function checkCountryWithNameExists(string $countryName): void
    {
        $this->checkFixtureExists($this->countries, 'Country', $countryName);
    }

    /**
     * @Given /^there is a state named "(.+)" with iso code "(.+)" in country "(.+)" and zone "(.+)"$/
     */
    public function createState($stateName, $stateIsoCode, $countryName, $zoneName)
    {
        $state = new State();
        $state->name = $stateName;
        $state->iso_code = $stateIsoCode;
        $state->id_zone = $this->getSharedStorage()->get($zoneName)->id;
        $state->id_country = $this->getSharedStorage()->get($countryName);
        $state->add();
        $this->states[$stateName] = $state;

        $this->getSharedStorage()->set($stateName, (int) $state->id);
    }

    /**
     * @param string $stateName
     *
     * @return State
     */
    public function getStateWithName(string $stateName): State
    {
        return $this->states[$stateName];
    }

    /**
     * @param string $stateName
     */
    public function checkStateWithNameExists(string $stateName): void
    {
        $this->checkFixtureExists($this->states, 'State', $stateName);
    }

    /**
     * @Given /^there is an address named "(.+)" with postcode "(.+)" in state "(.+)"$/
     */
    public function createAddress($addressName, $postCode, $stateName)
    {
        $this->checkStateWithNameExists($stateName);
        $address = new Address();
        $address->id_state = $this->states[$stateName]->id;
        $address->id_country = $this->states[$stateName]->id_country;
        $address->postcode = $postCode;
        $address->lastname = 'lastname';
        $address->firstname = 'firstname';
        $address->address1 = 'address1';
        $address->city = 'city';
        $address->alias = 'alias';
        $address->add();
        $this->addresses[$addressName] = $address;

        SharedStorage::getStorage()->set($addressName, $address->id);
    }

    /**
     * @Given /^address "(.+)" is associated to customer "(.+)"$/
     */
    public function setAddressCustomer($addressName, $customerName)
    {
        $this->checkAddressWithNameExists($addressName);
        $this->customerFeatureContext->checkCustomerWithNameExists($customerName);
        $this->addresses[$addressName]->id_customer = $this->customerFeatureContext->getCustomerWithName($customerName)->id;
        $this->addresses[$addressName]->update();
    }

    /**
     * @param string $addressName
     */
    public function checkAddressWithNameExists(string $addressName): void
    {
        $this->checkFixtureExists($this->addresses, 'Address', $addressName);
    }

    /**
     * @AfterScenario
     */
    public function cleanFixtures()
    {
        foreach ($this->priceRanges as $priceRange) {
            $priceRange->delete();
        }
        $this->priceRanges = [];
        foreach ($this->carriers as $carrier) {
            $carrier->delete();
        }
        $this->carriers = [];
        foreach ($this->addresses as $address) {
            $address->delete();
        }
        $this->addresses = [];
        foreach ($this->states as $state) {
            $state->delete();
        }
        $this->states = [];
        foreach ($this->countries as $countryName => $country) {
            $country->id_zone = $this->previousCountries[$countryName]->id_zone;
            $country->active = $this->previousCountries[$countryName]->active;
            $country->save();
        }
        $this->previousCountries = [];
        $this->countries = [];
        foreach ($this->zones as $zone) {
            $zone->delete();
        }
        $this->zones = [];
    }

    /**
     * @When /^I select carrier "(.+)" in my cart$/
     */
    public function setCartCarrier(string $carrierReference)
    {
        $this->getCurrentCart()->id_carrier = $this->getSharedStorage()->get($carrierReference);

        $this->getCurrentCart()->update();

        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
    }

    /**
     * @When /^I select address "(.+)" in my cart$/
     */
    public function setCartAddress($addresssName)
    {
        $this->checkAddressWithNameExists($addresssName);
        $this->getCurrentCart()->id_address_delivery = $this->addresses[$addresssName]->id;
        Context::getContext()->country = new Country((int) $this->addresses[$addresssName]->id_country);
    }

    /**
     * @Given a carrier :carrierReference with name :carrierName exists
     *
     * @param string $carrierReference
     * @param string $carrierName
     */
    public function checkExistingCarrier(string $carrierReference, string $carrierName)
    {
        $carriers = Carrier::getCarriers((int) Configuration::get('PS_LANG_DEFAULT'));
        foreach ($carriers as $carrier) {
            if ($carrier['name'] === $carrierName) {
                SharedStorage::getStorage()->set($carrierReference, (int) $carrier['id_carrier']);

                return;
            }
        }

        throw new RuntimeException(sprintf(
            'Could not find carrier with name %s',
            $carrierName
        ));
    }
}
