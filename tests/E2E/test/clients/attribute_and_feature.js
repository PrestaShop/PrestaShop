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
var CommonClient = require('./common_client');

class AttributeAndFeature extends CommonClient {

  clickOnAction(groupActionSelector, actionSelector, action = 'edit') {
    if (action === 'delete') {
      return this.client
        .waitForExistAndClick(groupActionSelector)
        .waitForExistAndClick(actionSelector)
        .alertAccept()
    } else {
      return this.client
        .waitForExistAndClick(groupActionSelector)
        .waitForExistAndClick(actionSelector)
    }
  }

  checkDeleted(selector) {
    return this.client
      .pause(3000)
      .then(() => this.client.isExisting(selector))
      .then((value) => expect(value).to.be.false);
  }

  checkOneExistence(valueToCheckWith, column = 'id') {
    let occurence = 0;
    for (let i = 0; i < elementsTable.length; i++) {
      if (elementsTable[i] === valueToCheckWith) {
        occurence = occurence + 1;
      }
    }
    if (column === 'position') {
      expect(occurence, 'there is no records or more than one having position 2').to.be.equal(1);
    } else {
      expect(occurence, 'there is no records or more than one having id 3').to.be.equal(1);
    }
  }
}

module.exports = AttributeAndFeature;
