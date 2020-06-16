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
const {Profiles} = require('@data/demo/profiles');
const {Languages} = require('@data/demo/languages');
const {Pages} = require('@data/demo/pages');

module.exports = class Employee {
  constructor(employeeToCreate = {}) {
    this.firstName = employeeToCreate.firstName || faker.name.firstName();
    this.lastName = employeeToCreate.lastName || faker.name.lastName();
    this.email = employeeToCreate.email || faker.internet.email(this.firstName, this.lastName, 'prestashop.com');
    this.password = employeeToCreate.password || 'prestashop_demo';
    this.defaultPage = employeeToCreate.defaultPage || faker.random.arrayElement(Pages);
    this.language = employeeToCreate.language
      || faker.random.arrayElement((Object.values(Languages).map(lang => lang.name)).slice(0, 2));
    this.active = employeeToCreate.active === undefined ? true : employeeToCreate.active;
    this.permissionProfile = employeeToCreate.permissionProfile || faker.random.arrayElement(Profiles);
  }
};
