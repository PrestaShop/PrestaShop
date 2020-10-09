const faker = require('faker');

const priceDisplayMethod = ['Tax included', 'Tax excluded'];

module.exports = class Group {
  constructor(groupToCreate = {}) {
    this.name = groupToCreate.name || faker.lorem.word();
    this.frName = groupToCreate.frName || this.name;
    this.discount = groupToCreate.discount || 0;
    this.priceDisplayMethod = groupToCreate.priceDisplayMethod || faker.random.arrayElement(priceDisplayMethod);
    this.shownPrices = groupToCreate.shownPrices === undefined ? true : groupToCreate.shownPrices;
  }
};
