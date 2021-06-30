const faker = require('faker');

/**
 * Create new delivery slip to use on creation form on delivery slip page on BO
 * @class
 */
class DeliverySlipData {
  /**
   * Constructor for class DeliverySlipData
   * @param deliverySlipOptions {Object} Could be used to force the value of some members
   */
  constructor(deliverySlipOptions = {}) {
    /** @type {string} Prefix to add to the delivery slip files */
    this.prefix = deliverySlipOptions.prefix || `#${faker.lorem.word()}`;

    /** @type {Number} Number of delivery slips created */
    this.number = deliverySlipOptions.number || faker.random.number({min: 10, max: 200}).toString();
  }
}

module.exports = DeliverySlipData;
