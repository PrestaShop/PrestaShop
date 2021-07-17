const faker = require('faker');

const {taxRules} = require('@data/demo/taxRule');
const {Zones} = require('@data/demo/zones');

const taxes = Object.values(taxRules).map(tax => tax.name);
const zonesID = Object.values(Zones).map(zone => zone.id);
const outOfRangeBehavior = ['Apply the cost of the highest defined range', 'Disable carrier'];
const billing = ['According to total price', 'According to total weight'];

module.exports = class Carrier {
  constructor(carrierToCreate = {}) {
    this.name = carrierToCreate.name || faker.company.companyName();
    this.transitName = carrierToCreate.transitName || faker.company.companyName();
    this.speedGrade = carrierToCreate.speedGrade || faker.random.number({min: 1, max: 9});
    this.trakingURL = carrierToCreate.trakingURL || 'http://example.com/track.php?num=20';
    this.handlingCosts = carrierToCreate.handlingCosts === undefined ? 'on' : carrierToCreate.handlingCosts;
    this.freeShipping = carrierToCreate.freeShipping === undefined ? 'on' : carrierToCreate.freeShipping;
    this.billing = carrierToCreate.billing || faker.random.arrayElement(billing);
    this.taxRule = carrierToCreate.taxRule || faker.random.arrayElement(taxes);
    this.outOfRangeBehavior = carrierToCreate.outOfRangeBehavior || faker.random.arrayElement(outOfRangeBehavior);
    this.rangeSup = carrierToCreate.rangeSup || faker.random.number({min: 1, max: 100});
    this.allZones = carrierToCreate.allZones === undefined ? 'on' : carrierToCreate.allZones;
    this.allZonesValue = carrierToCreate.allZonesValue || faker.random.number({min: 1, max: 100});
    this.zoneID = carrierToCreate.zoneID || faker.random.arrayElement(zonesID);
    this.maxWidth = carrierToCreate.maxWidth || faker.random.number({min: 1, max: 100});
    this.maxHeight = carrierToCreate.maxHeight || faker.random.number({min: 1, max: 100});
    this.maxDepth = carrierToCreate.maxDepth || faker.random.number({min: 1, max: 100});
    this.maxWeight = carrierToCreate.maxWeight || faker.random.number({min: 1, max: 100});
    this.enable = carrierToCreate.enable === undefined ? 'on' : carrierToCreate.enable;
  }
};
