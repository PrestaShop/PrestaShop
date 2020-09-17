const faker = require('faker');

module.exports = class Carrier {
  constructor(carrierToCreate = {}) {

    this.name = carrierToCreate.name || faker.commerce.department();
    this.transitName = carrierToCreate.transitName;
    this.speedGrade = carrierToCreate.speedGrade;
    this.trakingURL = carrierToCreate.trakingURL;
    this.freeShipping = carrierToCreate.freeShipping === undefined ? 'on' : carrierToCreate.freeShipping;
    this.taxRule = carrierToCreate.taxRule;
    this.outOfRangeBehavior = carrierToCreate.outOfRangeBehavior;
    this.maxWidth = carrierToCreate.maxWidth;
    this.maxHeight = carrierToCreate.maxHeight;
    this.maxDepth = carrierToCreate.maxDepth;
    this.maxWeight = carrierToCreate.maxWeight;
  }
};
