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

const genders = ['Mr.', 'Mrs.'];
const {groupAccess} = require('@data/demo/groupAccess');

module.exports = class Customer {
  constructor(customerToCreate = {}) {
    this.socialTitle = customerToCreate.socialTitle || faker.random.arrayElement(genders);
    this.firstName = customerToCreate.firstName || faker.name.firstName();
    this.lastName = customerToCreate.lastName || faker.name.lastName();
    this.email = customerToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.password = customerToCreate.password || '123456789';
    this.birthDate = faker.date.between('1950-01-01', '2000-12-31');
    this.yearOfBirth = customerToCreate.yearOfBirth || this.birthDate.getFullYear().toString();
    this.monthOfBirth = customerToCreate.monthOfBirth || (this.birthDate.getMonth() + 1).toString();
    this.dayOfBirth = customerToCreate.dayOfBirth || this.birthDate.getDate().toString();
    this.enabled = customerToCreate.enabled === undefined ? true : customerToCreate.enabled;
    this.partnerOffers = customerToCreate.partnerOffers === undefined ? true : customerToCreate.partnerOffers;
    this.defaultCustomerGroup = customerToCreate.defaultCustomerGroup || faker.random.arrayElement(groupAccess);
  }
};
