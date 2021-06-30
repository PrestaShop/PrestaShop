const faker = require('faker');

/**
 * Create new store to use on store creation form on BO
 * @class
 */
class StoreData {
  /**
   * Constructor for class StoreData
   * @param storeToCreate {Object} Could be used to force the value of some members
   */
  constructor(storeToCreate = {}) {
    /** @type {string} Name of the store */
    this.name = storeToCreate.name || faker.company.companyName();

    /** @type {string} First line address of the store */
    this.address1 = storeToCreate.address || faker.address.streetAddress();

    /** @type {string} Second line address of the store */
    this.address2 = storeToCreate.secondAddress || faker.address.secondaryAddress();

    /** @type {string} Postal code of the store */
    this.postcode = storeToCreate.postalCode || faker.address.zipCode('#####');

    /** @type {string} City for the address of the store */
    this.city = storeToCreate.city || faker.address.city();

    /** @type {string} Country of the address of the store */
    this.country = storeToCreate.country || 'France';

    /** @type {string} Latitude of the address of the store */
    this.latitude = storeToCreate.latitude || faker.address.latitude();

    /** @type {string} Longitude of the address of the store */
    this.longitude = storeToCreate.longitude || faker.address.longitude();

    /** @type {string} Phone number of the store (default format 01########) */
    this.phone = storeToCreate.phone || faker.phone.phoneNumber('01########');

    /** @type {string} Fax number of the store default format 01########) */
    this.fax = storeToCreate.fax || faker.phone.phoneNumber('01########');

    /** @type {string} Registration number of the store */
    this.registrationNumber = storeToCreate.registrationNumber || faker.finance.account();

    /** @type {string} Email to contact the store */
    this.email = storeToCreate.email || faker.internet.email();

    /** @type {string} Note to add information on the store */
    this.note = storeToCreate.note || faker.lorem.sentence();

    /** @type {boolean} Status of the store */
    this.status = storeToCreate.status === undefined ? true : storeToCreate.status;

    /** @type {Array<string>} Opening hours of the store */
    this.hours = storeToCreate.hours || new Array(7).fill('10:00 - 18:00', 0, 7);
  }
}

module.exports = StoreData;
