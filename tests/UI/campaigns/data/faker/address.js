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

module.exports = class Address {
  constructor(addressToCreate = {}) {
    this.email = addressToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.dni = addressToCreate.dni || '';
    this.alias = addressToCreate.alias || faker.address.streetAddress();
    this.firstName = addressToCreate.firstName || faker.name.firstName();
    this.lastName = addressToCreate.lastName || faker.name.lastName();
    this.company = (addressToCreate.company || faker.company.companyName()).substring(0, 63);
    this.vatNumber = addressToCreate.vatNumber || '';
    this.address = addressToCreate.address || faker.address.streetAddress();
    this.secondAddress = addressToCreate.secondAddress || faker.address.secondaryAddress();
    this.postalCode = addressToCreate.postalCode || faker.address.zipCode('#####');
    this.city = addressToCreate.city || faker.address.city();
    this.country = addressToCreate.country || faker.random.arrayElement(countries);
    this.phone = addressToCreate.homePhone || faker.phone.phoneNumber('01########');
    this.other = addressToCreate.other || '';
  }
};
