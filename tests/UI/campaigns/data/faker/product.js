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

module.exports = class Product {
  constructor(productToCreate) {
    this.name = (productToCreate.name || faker.commerce.productName()).toUpperCase();
    this.type = productToCreate.type;
    this.status = productToCreate.status === undefined ? true : productToCreate.status;
    this.summary = productToCreate.summary === undefined ? faker.lorem.sentence() : productToCreate.summary;
    this.description = productToCreate.description === undefined ? faker.lorem.sentence() : productToCreate.description;
    this.reference = faker.random.alphaNumeric(7);
    this.quantity = productToCreate.quantity === undefined
      ? faker.random.number({min: 1, max: 9})
      : productToCreate.quantity;
    this.price = productToCreate.price === undefined ? faker.random.number({min: 10, max: 20}) : productToCreate.price;
    this.combinations = productToCreate.combinations || {
      Color: ['White', 'Black'],
      Size: ['S', 'M'],
    };
    this.taxRule = productToCreate.taxRule || 'FR Taux standard (20%)';
    this.specificPrice = productToCreate.specificPrice || {
      combinations: 'Size - S, Color - White',
      discount: faker.random.number({min: 10, max: 100}),
      startingAt: faker.random.number({min: 2, max: 5}),
    };
  }
};
