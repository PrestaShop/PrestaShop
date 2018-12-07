<?php
/**
 * 2007-2018 PrestaShop.
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

namespace LegacyTests\Unit\Core\Cart\Calculation\Carrier;

use Address;
use Cache;
use Carrier;
use CartRule;
use Configuration;
use Country;
use Db;
use RangePrice;
use State;
use LegacyTests\Unit\Core\Cart\Calculation\AbstractCartCalculationTest;
use Zone;

abstract class AbstractCarrierTest extends AbstractCartCalculationTest
{
    const ZONE_FIXTURES = array(
        1 => array(
            'name' => 'zone #1',
        ),
        2 => array(
            'name' => 'zone #2',
        ),
    );

    const COUNTRY_FIXTURES = array(
        'FR' => array(
            'zoneId' => 1,
        ),
        'US' => array(
            'zoneId' => 2,
        ),
    );

    const STATE_FIXTURES = array(
        1 => array(
            'zoneId' => 1,
            'countryIsoCode' => 'FR',
            'name' => 'state #1',
            'isoCode' => 'TEST-1',
        ),
        2 => array(
            'zoneId' => 2,
            'countryIsoCode' => 'US',
            'name' => 'state #2',
            'isoCode' => 'TEST-2',
        ),
    );

    const ADDRESS_FIXTURES = array(
        1 => array(
            'countryIsoCode' => 'FR',
            'stateId' => 1,
            'postcode' => 1,
        ),
        2 => array(
            'countryIsoCode' => 'US',
            'stateId' => 2,
            'postcode' => 1,
        ),
    );

    const CARRIER_FIXTURES = array(
        1 => array(
            'name' => 'carrier 1',
            'isFree' => false,
            'ranges' => array(
                1 => array(
                    'from' => 0,
                    'to' => 10000,
                    'shippingPrices' => array(
                        1 => 3.1, // zoneId => price
                        2 => 4.3, // zoneId => price
                    ),
                ),
            ),
        ),
        2 => array(
            'name' => 'carrier 2',
            'isFree' => false,
            'ranges' => array(
                1 => array(
                    'from' => 0,
                    'to' => 10000,
                    'shippingPrices' => array(
                        1 => 5.7, // zoneId => price
                        2 => 6.2, // zoneId => price
                    ),
                ),
            ),
        ),
    );

    /**
     * @var Carrier[]
     */
    protected $carriers = array();

    /**
     * @var RangePrice[]
     */
    protected $priceRanges = array();

    /**
     * @var Zone[]
     */
    protected $zones = array();

    /**
     * @var array array of isoCode => previousZoneId
     */
    protected $countries = array();

    /**
     * @var State[]
     */
    protected $states = array();

    /**
     * @var Address[]
     */
    protected $addresses = array();

    public function setUp()
    {
        parent::setUp();

        $this->resetCart();
        $this->insertAddresses();
        $this->insertCarriers();

        // adds the specific carrier rules to cart rules
        foreach (static::CART_RULES_FIXTURES as $k => $cartRuleData) {
            $cartRule = $this->getCartRuleFromFixtureId($k);
            if (!empty($cartRuleData['carrierRestrictionIds'])) {
                foreach ($cartRuleData['carrierRestrictionIds'] as $carrierId) {
                    $carrier = $this->getCarrierFromFixtureId($carrierId);
                    Db::getInstance()->execute(
                        '
                      INSERT INTO '._DB_PREFIX_."cart_rule_carrier(`id_cart_rule`, `id_carrier`)
                      VALUES('".(int) $cartRule->id."',
                      '".(int) $carrier->id."')
                    "
                    );
                }
            }
            $this->cartRules[$k] = $cartRule;
        }
        Cache::clear();
    }

    public function tearDown()
    {
        $this->cart->id_carrier = 0;
        $this->cart->id_address_delivery = 0;

        foreach ($this->carriers as $carrier) {
            $carrier->delete();
        }

        foreach ($this->priceRanges as $range) {
            $range->delete();
        }

        foreach ($this->addresses as $address) {
            $address->delete();
        }

        foreach ($this->states as $state) {
            $state->delete();
        }

        foreach ($this->countries as $isoCode => $previousZoneId) {
            $countryId = Country::getByIso($isoCode);
            if (!$countryId) {
                throw new \Exception('Country not found with iso code = '.$isoCode);
            }
            $country = new Country($countryId);
            // restore pevious value
            $country->id_zone = $this->countries[$isoCode];
            $country->save();
        }

        foreach ($this->zones as $zone) {
            $zone->delete();
        }

        parent::tearDown();
    }

    protected function insertCarriers()
    {
        foreach (static::CARRIER_FIXTURES as $k => $carrierFixture) {
            $carrier = new Carrier(null, Configuration::get('PS_LANG_DEFAULT'));
            $carrier->name = $carrierFixture['name'];
            $carrier->shipping_method = Carrier::SHIPPING_METHOD_PRICE;
            $carrier->delay = '28 days later';
            $carrier->active = 1;
            $carrier->add();
            $carrierPrices = array();
            foreach ($carrierFixture['ranges'] as $rangeFixture) {
                $range = new RangePrice();
                $range->id_carrier = $carrier->id;
                $range->delimiter1 = $rangeFixture['from'];
                $range->delimiter2 = $rangeFixture['to'];
                $range->add();
                $this->priceRanges[] = $range;
                foreach ($rangeFixture['shippingPrices'] as $zoneId => $price) {
                    $zone = $this->getZoneFromFixtureId($zoneId);
                    if (null === $zone) {
                        throw new \Exception('Zone not found with fixture id = '.$zoneId);
                    }
                    $carrierPrices[] = array(
                        'id_range_price' => (int) $range->id,
                        'id_range_weight' => null,
                        'id_carrier' => (int) $carrier->id,
                        'id_zone' => (int) $zone->id,
                        'price' => $price,
                    );
                }
            }

            if (!empty($carrierPrices)) {
                $carrier->addDeliveryPrice($carrierPrices, true);
            }
            $this->carriers[$k] = $carrier;
        }
    }

    /**
     * @param int $id fixture id
     *
     * @return null|Carrier
     */
    protected function getCarrierFromFixtureId($id)
    {
        if (isset($this->carriers[$id])) {
            return $this->carriers[$id];
        }

        return;
    }

    protected function insertAddresses()
    {
        foreach (static::ZONE_FIXTURES as $k => $zoneFixture) {
            $zone = new Zone();
            $zone->name = $zoneFixture['name'];
            $zone->add();
            $this->zones[$k] = $zone;
        }
        foreach (static::COUNTRY_FIXTURES as $isoCode => $countryFixture) {
            $country = $this->getCountryFromIsoCode($isoCode);
            // store pevious value
            $this->countries[$isoCode] = $country->id_zone;
            $zone = $this->getZoneFromFixtureId($countryFixture['zoneId']);
            if (null === $zone) {
                throw new \Exception('Zone not found with fixture id = '.$countryFixture['zoneId']);
            }
            $country->id_zone = $zone->id;
            $country->active = 1;
            $country->save();
        }
        foreach (static::STATE_FIXTURES as $k => $stateFixture) {
            $state = new State();
            $state->name = $stateFixture['name'];
            $state->iso_code = $stateFixture['isoCode'];
            $zone = $this->getZoneFromFixtureId($stateFixture['zoneId']);
            if (null === $zone) {
                throw new \Exception('Zone not found with fixture id = '.$stateFixture['zoneId']);
            }
            $state->id_zone = $zone->id;
            $country = $this->getCountryFromIsoCode($stateFixture['countryIsoCode']);
            if (null === $country) {
                throw new \Exception('Country not found with fixture iso code = '.$stateFixture['countryIsoCode']);
            }
            $state->id_country = $country->id;
            $state->add();
            $this->states[$k] = $state;
        }
        foreach (static::ADDRESS_FIXTURES as $k => $addressFixture) {
            $address = new Address();
            $country = $this->getCountryFromIsoCode($addressFixture['countryIsoCode']);
            if (null === $country) {
                throw new \Exception('Country not found with fixture iso code = '.$addressFixture['countryIsoCode']);
            }
            $address->id_country = $country->id;
            $state = $this->getStateFromFixtureId($addressFixture['stateId']);
            if (null === $state) {
                throw new \Exception('State not found with fixture id = '.$addressFixture['stateId']);
            }
            $address->id_state = $state->id;
            $address->postcode = $addressFixture['postcode'];
            $address->lastname = 'lastname';
            $address->firstname = 'firstname';
            $address->address1 = 'address1';
            $address->city = 'city';
            $address->alias = 'alias';
            $address->add();
            $this->addresses[$k] = $address;
        }
    }

    /**
     * @param int $id fixture id
     *
     * @return null|Zone
     */
    protected function getZoneFromFixtureId($id)
    {
        if (isset($this->zones[$id])) {
            return $this->zones[$id];
        }

        return;
    }

    /**
     * @param int $id fixture id
     *
     * @return null|Country
     */
    protected function getCountryFromIsoCode($isoCode)
    {
        $countryId = Country::getByIso($isoCode);
        if (!$countryId) {
            throw new \Exception('Country not found with iso code = '.$isoCode);
        }
        $country = new Country($countryId);

        return $country;
    }

    /**
     * @param int $id fixture id
     *
     * @return null|State
     */
    protected function getStateFromFixtureId($id)
    {
        if (isset($this->states[$id])) {
            return $this->states[$id];
        }

        return;
    }

    /**
     * @param int $id fixture id
     *
     * @return null|Address
     */
    protected function getAddressFromFixtureId($id)
    {
        if (isset($this->addresses[$id])) {
            return $this->addresses[$id];
        }

        return;
    }

    protected function setCartCarrierFromFixtureId($carrierFixtureId)
    {
        if (0 == $carrierFixtureId) {
            $this->cart->id_carrier = 0;

            return;
        }

        $carrier = $this->getCarrierFromFixtureId($carrierFixtureId);
        if (null === $carrier) {
            throw new \Exception('Carrier not found with fixture id = '.$carrierFixtureId);
        }
        $this->cart->id_carrier = $carrier->id;

        $this->cart->update();

        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
    }

    protected function setCartAddress($addressId)
    {
        if (0 == $addressId) {
            $this->cart->id_address_delivery = 0;

            return;
        }

        $address = $this->getAddressFromFixtureId($addressId);
        if (null === $address) {
            throw new \Exception('Address not found with fixture id = '.$addressId);
        }
        $this->cart->id_address_delivery = $address->id;
    }
}
