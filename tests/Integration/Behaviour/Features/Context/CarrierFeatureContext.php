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
use Group;
use RangePrice;
use RangeWeight;
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
     * @Given /^there is a zone named "(.+)"$/
     */
    public function createZone($zoneName)
    {
        $zone = new Zone();
        $zone->name = $zoneName;
        $zone->add();
        $this->zones[$zoneName] = $zone;
    }

    /**
     * @param string $zoneName
     */
    public function checkZoneWithNameExists(string $zoneName): void
    {
        $this->checkFixtureExists($this->zones, 'Zone', $zoneName);
    }

    /**
     * @Given /^there is a country named "(.+)" and iso code "(.+)" in zone "(.+)"$/
     */
    public function createCountry($countryName, $isoCode, $zoneName)
    {
        $this->checkZoneWithNameExists($zoneName);
        $countryId = Country::getByIso($isoCode, false);
        if (!$countryId) {
            throw new \Exception('Country not found with iso code = ' . $isoCode);
        }
        $country = new Country($countryId);
        // clone country to be able to properly reset previous data
        $this->previousCountries[$countryName] = clone $country;
        $this->countries[$countryName] = $country;
        $country->id_zone = $this->zones[$zoneName]->id;
        $country->active = true;
        $country->save();
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
        $this->checkZoneWithNameExists($zoneName);
        $this->checkCountryWithNameExists($countryName);
        $state = new State();
        $state->name = $stateName;
        $state->iso_code = $stateIsoCode;
        $state->id_zone = $this->zones[$zoneName]->id;
        $state->id_country = $this->countries[$countryName]->id;
        $state->add();
        $this->states[$stateName] = $state;
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
     * @Given /^there is a carrier named "(.+)"$/
     */
    public function createCarrier($carrierName)
    {
        $carrier = new Carrier(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $carrier->name = $carrierName;
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_PRICE;
        $carrier->delay = '28 days later';
        $carrier->active = true;
        $carrier->add();
        $this->carriers[$carrierName] = $carrier;
        SharedStorage::getStorage()->set($carrierName, (int) $carrier->id);

        $groups = Group::getGroups(Context::getContext()->language->id);
        $groupIds = [];
        foreach ($groups as $group) {
            $groupIds[] = $group['id_group'];
        }
        $carrier->setGroups($groupIds);
    }

    /**
     * @Given /^carrier "(.+)" ships to all groups$/
     */
    public function setCarrierShipsToAllGroups($carrierName)
    {
        $this->checkCarrierWithNameExists($carrierName);
        $carrier = $this->carriers[$carrierName];

        $groups = Group::getGroups(Context::getContext()->language->id);
        $groupIds = [];
        foreach ($groups as $group) {
            $groupIds[] = $group['id_group'];
        }
        $carrier->setGroups($groupIds);
    }

    /**
     * @Given /^the carrier "(.+)" uses "(.+)" as tracking url$/
     */
    public function setCarrierTrackingUrl(string $carrierName, string $url): void
    {
        $this->checkCarrierWithNameExists($carrierName);
        $carrier = $this->carriers[$carrierName];
        $carrier->url = $url;
        $carrier->save();
    }

    /**
     * @param string $carrierName
     */
    public function checkCarrierWithNameExists(string $carrierName): void
    {
        $this->checkFixtureExists($this->carriers, 'Carrier', $carrierName);
    }

    /**
     * @param string $carrierName
     *
     * @return Carrier
     */
    public function getCarrierWithName(string $carrierName): Carrier
    {
        return $this->carriers[$carrierName];
    }

    /**
     * Be careful: this method REPLACES shipping fees for carrier
     *
     * @Given /^carrier "(.+)" applies shipping fees of (\d+\.\d+) in zone "(.+)" for (weight|price) between (\d+) and (\d+)$/
     */
    public function setCarrierFees($carrierName, $shippingPrice, $zoneName, $rangeType, $from, $to)
    {
        $this->checkCarrierWithNameExists($carrierName);
        $this->checkZoneWithNameExists($zoneName);
        if (empty($this->carriers[$carrierName]->getZone((int) $this->zones[$zoneName]->id))) {
            $this->carriers[$carrierName]->addZone((int) $this->zones[$zoneName]->id);
        }
        $rangeClass = $rangeType == 'weight' ? RangeWeight::class : RangePrice::class;
        $primary = $rangeType == 'weight' ? 'id_range_weight' : 'id_range_price';
        $rangeRows = $rangeClass::getRanges($this->carriers[$carrierName]->id);
        $rangeId = false;
        foreach ($rangeRows as $rangeRow) {
            if ($rangeRow['delimiter1'] == $from) {
                $rangeId = $rangeRow[$primary];
            }
        }
        if (!empty($rangeId)) {
            $range = new $rangeClass($rangeId);
        } else {
            $range = new $rangeClass();
            $range->id_carrier = $this->carriers[$carrierName]->id;
            $range->delimiter1 = $from;
            $range->delimiter2 = $to;
            $range->add();
            $this->priceRanges[] = $range;
        }
        $carrierPriceRange = [
            'id_range_price' => (int) $range->id,
            'id_range_weight' => null,
            'id_carrier' => (int) $this->carriers[$carrierName]->id,
            'id_zone' => (int) $this->zones[$zoneName]->id,
            'price' => $shippingPrice,
        ];
        $this->carriers[$carrierName]->addDeliveryPrice([$carrierPriceRange], true);
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
    public function setCartCarrier($carrierName)
    {
        $this->checkCarrierWithNameExists($carrierName);
        $this->getCurrentCart()->id_carrier = $this->carriers[$carrierName]->id;

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

    /**
     * @Given I enable carrier :carrierReference
     *
     * @param string $carrierReference
     */
    public function enableCarrier(string $carrierReference)
    {
        $carrierId = SharedStorage::getStorage()->get($carrierReference);
        $carrier = new Carrier($carrierId);
        $carrier->active = true;
        $carrier->save();
        // Reset cache so that the carrier becomes selectable
        Carrier::resetStaticCache();
    }

    /**
     * @Then I associate the tax rule group :taxRulesGroupReference to carrier :carrierReference
     *
     * @param string $taxRulesGroupReference
     * @param string $carrierReference
     */
    public function associateCarrierTaxRulesGroup(string $taxRulesGroupReference, string $carrierReference)
    {
        $carrierId = SharedStorage::getStorage()->get($carrierReference);
        $taxRulesGroupId = SharedStorage::getStorage()->get($taxRulesGroupReference);
        $carrier = new Carrier($carrierId);
        $carrier->setTaxRulesGroup($taxRulesGroupId);
    }
}
