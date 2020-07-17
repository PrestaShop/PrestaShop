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

const {groupAccess} = require('@data/demo/groupAccess');
const {countries} = require('@data/demo/countries');

const currencies = ['All currencies', 'Euro'];
const reductionType = ['Amount', 'Percentage'];
const reductionTax = ['Tax excluded', 'Tax included'];

module.exports = class Category {
  constructor(priceRuleToCreate = {}) {
    this.name = priceRuleToCreate.name || faker.commerce.department();
    this.currency = priceRuleToCreate.currency || faker.random.arrayElement(currencies);
    this.country = priceRuleToCreate.country || faker.random.arrayElement(countries);
    this.group = priceRuleToCreate.group || faker.random.arrayElement(groupAccess);
    this.fromQuantity = priceRuleToCreate.fromQuantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : priceRuleToCreate.fromQuantity;
    this.reductionType = priceRuleToCreate.reductionType || faker.random.arrayElement(reductionType);
    this.reductionTax = priceRuleToCreate.reductionTax || faker.random.arrayElement(reductionTax);
    this.reduction = priceRuleToCreate.reduction || faker.random.number({min: 20, max: 30});
  }
};
