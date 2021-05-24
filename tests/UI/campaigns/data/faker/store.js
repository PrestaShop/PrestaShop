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
    /** @member {string} Name of the store */
    this.name = storeToCreate.name || faker.company.companyName();

    /** @member {string} First line address of the store */
    this.address1 = storeToCreate.address || faker.address.streetAddress();

    /** @member {string} Second line address of the store */
    this.address2 = storeToCreate.secondAddress || faker.address.secondaryAddress();

    /** @member {string} Postal code of the store */
    this.postcode = storeToCreate.postalCode || faker.address.zipCode('#####');

    /** @member {string} City for the address of the store */
    this.city = storeToCreate.city || faker.address.city();

    /** @member {string} Country of the address of the store */
    this.country = storeToCreate.country || 'France';

    /** @member {string} Latitude of the address of the store */
    this.latitude = storeToCreate.latitude || faker.address.latitude();

    /** @member {string} Longitude of the address of the store */
    this.longitude = storeToCreate.longitude || faker.address.longitude();

    /** @member {string} Phone number of the store (default format 01########) */
    this.phone = storeToCreate.phone || faker.phone.phoneNumber('01########');

    /** @member {string} Fax number of the store default format 01########) */
    this.fax = storeToCreate.fax || faker.phone.phoneNumber('01########');

    /** @member {string} Registration number of the store */
    this.registrationNumber = storeToCreate.registrationNumber || faker.finance.account();

    /** @member {string} Email to contact the store */
    this.email = storeToCreate.email || faker.internet.email();

    /** @member {string} Note to add information on the store */
    this.note = storeToCreate.note || faker.lorem.sentence();

    /** @member {boolean} Status of the store */
    this.status = storeToCreate.status === undefined ? true : storeToCreate.status;

    /** @member {Array<string>} Opening hours of the store */
    this.hours = storeToCreate.hours || new Array(7).fill('10:00 - 18:00', 0, 7);
  }
}

module.exports = StoreData;
