/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
var Product = require('./product');
const {AddProductPage} = require('../../selectors/BO/add_product_page');
var data = require('./../../datas/product-data');
var path = require('path');

class CreateCombinations extends Product {

  createCombination(size, color) {
    return this.client
      .pause(3000)
      .waitForExistAndClick(size)
      .pause(3000)
      .waitForExistAndClick(color)
      .pause(3000)
  }

  getCombinationData(number, pause = 2000) {
    return this.client
      .pause(pause)
      .waitForExist(AddProductPage.combination_panel.replace('%NUMBER', number), 90000)
      .then(() => this.client.getAttribute(AddProductPage.combination_panel.replace('%NUMBER', number), 'data'))
      .then((text) => global.combinationId = text);
  }

  goToEditCombination() {
    return this.client
      .waitForVisibleAndClick(AddProductPage.combination_edit.replace('%NUMBER', global.combinationId))
      .pause(2000)
  }

  editCombination(number) {
    return this.client
      .waitAndSetValue(AddProductPage.combination_quantity.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].quantity)
      .waitAndSetValue(AddProductPage.combination_available_date.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].available_date)
      .waitAndSetValue(AddProductPage.combination_min_quantity.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].minimal_quantity)
      .waitAndSetValue(AddProductPage.combination_reference.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].ref)
      .waitAndSetValue(AddProductPage.combination_whole_sale.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].wholesale)
      .waitAndSetValue(AddProductPage.combination_low_stock.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].minimal_quantity)
      .waitAndSetValue(AddProductPage.combination_priceTI.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].priceTI)
      .waitAndSetValue(AddProductPage.combination_attribute_unity.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].unity)
      .waitAndSetValue(AddProductPage.combination_attribute_weight.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].weight)
      .waitAndSetValue(AddProductPage.combination_attribute_isbn.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].isbn)
      .waitAndSetValue(AddProductPage.combination_attribute_ean13.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].ean13)
      .waitAndSetValue(AddProductPage.combination_attribute_upc.replace('%NUMBER', global.combinationId), data.standard.variations[number - 1].upc)
      .scrollWaitForExistAndClick(AddProductPage.combination_image.replace('%NUMBER', global.combinationId))
      .then(() => this.client.getAttribute(AddProductPage.combination_image.replace('%NUMBER', global.combinationId), 'title'))
      .then((title) => global.title_image = title)
  }

  backToProduct() {
    return this.client
      .scrollTo(AddProductPage.back_to_product.replace('%NUMBER', global.combinationId), 50)
      .waitForExistAndClick(AddProductPage.back_to_product.replace('%NUMBER', global.combinationId))
      .pause(2000)
  }
}

module.exports = CreateCombinations;
