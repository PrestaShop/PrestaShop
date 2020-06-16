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

module.exports = class brandAddress {
  constructor(brandAddressToCreate = {}) {
    this.brandName = brandAddressToCreate.brandName || '--';
    this.firstName = brandAddressToCreate.firstName || faker.name.firstName();
    this.lastName = brandAddressToCreate.lastName || faker.name.lastName();
    this.address = brandAddressToCreate.address || faker.address.streetAddress();
    this.secondaryAddress = brandAddressToCreate.secondaryAddress || faker.address.secondaryAddress();
    this.postalCode = brandAddressToCreate.postalCode || faker.address.zipCode();
    this.city = brandAddressToCreate.city || faker.address.city();
    this.country = brandAddressToCreate.country || faker.random.arrayElement(countries);
    this.homePhone = brandAddressToCreate.homePhone || faker.phone.phoneNumber('01########');
    this.mobilePhone = brandAddressToCreate.mobilePhone || faker.phone.phoneNumber('06########');
    this.other = brandAddressToCreate.other || '';
  }
};
