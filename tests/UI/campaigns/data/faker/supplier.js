const faker = require('faker');
const {countries} = require('@data/demo/countries');

const countriesNames = Object.values(countries).map(country => country.name);

/**
 * Create new supplier to use on supplier creation form on BO
 * @class
 */
class SupplierData {
  /**
   * Constructor for class SupplierData
   * @param supplierToCreate {Object} Could be used to force the value of some members
   */
  constructor(supplierToCreate = {}) {
    /** @type {string} Name of the supplier */
    this.name = (supplierToCreate.name || faker.company.companyName()).substring(0, 63);

    /** @type {string} Description of the supplier */
    this.description = supplierToCreate.description || faker.lorem.sentence();

    /** @type {string} French description of the supplier */
    this.descriptionFr = supplierToCreate.descriptionFr || this.description;

    /** @type {string} Home phone number of the supplier (default format 01########) */
    this.homePhone = supplierToCreate.homePhone || faker.phone.phoneNumber('01########');

    /** @type {string} Mobile phone number of the supplier (default format 01########) */
    this.mobilePhone = supplierToCreate.mobilePhone || faker.phone.phoneNumber('06########');

    /** @type {string} First line address of the supplier */
    this.address = supplierToCreate.address || faker.address.streetAddress();

    /** @type {string} Second line address of the supplier */
    this.secondaryAddress = supplierToCreate.secondaryAddress || faker.address.secondaryAddress();

    /** @type {string} Postal code of the supplier */
    this.postalCode = supplierToCreate.postalCode || faker.address.zipCode().replace('.', '-');

    /** @type {string} City for the address of the supplier */
    this.city = supplierToCreate.city || faker.address.city();

    /** @type {string} Country for the address of the supplier */
    this.country = supplierToCreate.country || faker.random.arrayElement(countriesNames);

    /** @type {string} Logo name/path of the supplier */
    this.logo = supplierToCreate.logo || `${this.name.replace(/[^\w\s]/gi, '')}.png`;

    /** @type {string} Meta title of the supplier */
    this.metaTitle = supplierToCreate.metaTitle || this.name;

    /** @type {string} French meta title of the supplier */
    this.metaTitleFr = supplierToCreate.metaTitleFr || this.metaTitle;

    /** @type {string} Meta description of the supplier */
    this.metaDescription = supplierToCreate.metaDescription || faker.lorem.sentence();

    /** @type {string} French meta description of the supplier */
    this.metaDescriptionFr = supplierToCreate.metaDescriptionFr || this.metaDescription;

    /** @type {Array<string>} Meta keywords of the supplier */
    this.metaKeywords = supplierToCreate.metaKeywords || [faker.lorem.word(), faker.lorem.word()];

    /** @type {Array<string>} French meta keywords of the supplier */
    this.metaKeywordsFr = supplierToCreate.metaKeywordsFr || this.metaKeywords;

    /** @type {boolean} Status of the supplier */
    this.enabled = supplierToCreate.enabled === undefined ? true : supplierToCreate.enabled;

    /** @type {number} Number of products associated */
    this.products = supplierToCreate.products || 0;
  }
}

module.exports = SupplierData;
