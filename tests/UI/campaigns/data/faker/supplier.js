/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
const faker = require('faker');

const {countries} = require('@data/demo/countries');

module.exports = class Supplier {
  constructor(supplierToCreate = {}) {
    this.name = (supplierToCreate.name || faker.company.companyName()).substring(0, 63);
    this.description = supplierToCreate.description || faker.lorem.sentence();
    this.descriptionFr = supplierToCreate.descriptionFr || this.description;
    this.homePhone = supplierToCreate.homePhone || faker.phone.phoneNumber('01########');
    this.mobilePhone = supplierToCreate.mobilePhone || faker.phone.phoneNumber('06########');
    this.address = supplierToCreate.address || faker.address.streetAddress();
    this.secondaryAddress = supplierToCreate.secondaryAddress || faker.address.secondaryAddress();
    this.postalCode = supplierToCreate.postalCode || faker.address.zipCode().replace('.', '-');
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
