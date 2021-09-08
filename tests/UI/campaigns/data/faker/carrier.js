const faker = require('faker');

const {taxRules} = require('@data/demo/taxRule');
const {Zones} = require('@data/demo/zones');

const taxes = Object.values(taxRules).map(tax => tax.name);
const zonesID = Object.values(Zones).map(zone => zone.id);
const outOfRangeBehavior = ['Apply the cost of the highest defined range', 'Disable carrier'];
const billing = ['According to total price', 'According to total weight'];

/**
 * Create new carrier to use in carrier form on BO
 * @class
 */
class CarrierData {
  /**
   * Constructor for class CarrierData
   * @param carrierToCreate {object} Could be used to force the value of some members
   */
  constructor(carrierToCreate = {}) {
    /** @type {string} Name of the carrier */
    this.name = carrierToCreate.name || faker.company.companyName();

    /** @type {string} Transit name of the carrier */
    this.transitName = carrierToCreate.transitName || faker.company.companyName();

    /** @type {number} Shipping delay, 0 for longest and 9 for shortest */
    this.speedGrade = carrierToCreate.speedGrade || faker.random.number({min: 1, max: 9});

    /** @type {string} Url of carrier tracking */
    this.trakingURL = carrierToCreate.trakingURL || 'http://example.com/track.php?num=20';

    /** @type {boolean} True to include handling costs on the price */
    this.handlingCosts = carrierToCreate.handlingCosts === undefined ? 'on' : carrierToCreate.handlingCosts;

    /** @type {string} True to make shipping free */
    this.freeShipping = carrierToCreate.freeShipping === undefined ? 'on' : carrierToCreate.freeShipping;

    /** @type {string} Billing method of the carrier */
    this.billing = carrierToCreate.billing || faker.random.arrayElement(billing);

    /** @type {string} Tax rule of the carrier */
    this.taxRule = carrierToCreate.taxRule || faker.random.arrayElement(taxes);

    /** @type {string} Behavior when no defined range matches the customer carts */
    this.outOfRangeBehavior = carrierToCreate.outOfRangeBehavior || faker.random.arrayElement(outOfRangeBehavior);

    /** @type {number} Superior range for the carrier */
    this.rangeSup = carrierToCreate.rangeSup || faker.random.number({min: 1, max: 100});

    /** @type {boolean} True to apply it to all zones */
    this.allZones = carrierToCreate.allZones === undefined ? 'on' : carrierToCreate.allZones;

    /** @type {number} Value to set when all zones is checked */
    this.allZonesValue = carrierToCreate.allZonesValue || faker.random.number({min: 1, max: 100});

    /** @type {string} ID of the zone on carrier form */
    this.zoneID = carrierToCreate.zoneID || faker.random.arrayElement(zonesID);

    /** @type {string} Max width that the carrier can handle */
    this.maxWidth = carrierToCreate.maxWidth || faker.random.number({min: 1, max: 100});

    /** @type {string} Max height that the carrier can handle */
    this.maxHeight = carrierToCreate.maxHeight || faker.random.number({min: 1, max: 100});

    /** @type {string} Max depth that the carrier can handle */
    this.maxDepth = carrierToCreate.maxDepth || faker.random.number({min: 1, max: 100});

    /** @type {string} Max weight that the carrier can handle */
    this.maxWeight = carrierToCreate.maxWeight || faker.random.number({min: 1, max: 100});

    /** @type {boolean} Status of the carrier */
    this.enable = carrierToCreate.enable === undefined ? true : carrierToCreate.enable;
  }
}

module.exports = CarrierData;
