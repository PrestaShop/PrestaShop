const faker = require('faker');

const countries = ['France', 'Netherlands', 'United Kingdom', 'Germany'];

module.exports = class Supplier {
  constructor(supplierToCreate = {}) {
    this.name = supplierToCreate.name || faker.company.companyName();
    this.description = supplierToCreate.description || faker.lorem.sentence();
    this.descriptionFr = supplierToCreate.descriptionFr || this.description;
    this.homePhone = supplierToCreate.homePhone || faker.phone.phoneNumber('01########');
    this.mobilePhone = supplierToCreate.mobilePhone || faker.phone.phoneNumber('06########');
    this.address = supplierToCreate.address || faker.address.streetAddress();
    this.secondaryAddress = supplierToCreate.secondaryAddress || faker.address.secondaryAddress();
    this.postalCode = supplierToCreate.postalCode || faker.address.zipCode();
    this.city = supplierToCreate.city || faker.address.city();
    this.country = supplierToCreate.country || faker.random.arrayElement(countries);
    this.logo = `${this.name.replace(/[^\w\s]/gi, '')}.png`;
    this.metaTitle = supplierToCreate.metaTitle || this.name;
    this.metaTitleFr = supplierToCreate.metaTitleFr || this.metaTitle;
    this.metaDescription = supplierToCreate.metaDescription || faker.lorem.sentence();
    this.metaDescriptionFr = supplierToCreate.metaDescriptionFr || this.metaDescription;
    this.metaKeywords = supplierToCreate.metaKeywords || [faker.lorem.word(), faker.lorem.word()];
    this.metaKeywordsFr = supplierToCreate.metaKeywordsFr || this.metaKeywords;
    this.enabled = supplierToCreate.enabled === undefined ? true : supplierToCreate.enabled;
    this.products = supplierToCreate.products || 0;
  }
};
