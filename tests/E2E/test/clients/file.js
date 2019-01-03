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

global.fileLists = [];
global.fileSortedLists = [];

class File extends CommonClient {

  searchByValue(searchInput, searchButton, value) {
    if(global.isVisible) {
      return this.client
        .waitAndSetValue(searchInput, value)
        .waitForExistAndClick(searchButton);
    }
  }

  resetButton(selector, filtredTable) {
    if(global.isVisible && !filtredTable) {
      return this.client
        .waitForExistAndClick(selector);
    }
  }

  getFilesNumber(selector, pause = 0) {
    if(global.isVisible) {
      return this.client
        .pause(pause)
        .then(() => {
          global.filesNumber = 0;
        });
    } else {
      return this.client
        .pause(pause)
        .execute(function (selector) {
          let count = document.getElementById(selector).getElementsByTagName("tbody")[0].children.length;
          return count;
        }, selector)
        .then((count) => {
          global.filesNumber = count.value;
        });
    }
  }

  getFileInformations(selector, index, lowerCase = true, pause = 0, timeout = 90000) {
    return this.client
      .pause(pause)
      .waitForExist(selector, timeout)
      .getText(selector)
      .then((name) => {
        if(lowerCase) {
          global.fileLists[index] = name.toLowerCase();
          global.fileSortedLists[index] = name.toLowerCase();
        } else {
          global.fileLists[index] = name;
        }
      });
  }

  checkSortFile() {
    return this.client
      .pause(1000)
      .then(() => {
        this.client
          .waitUntil(function () {
            expect(fileLists.sort()).to.deep.equal(fileSortedLists);
          }, 1000 * global.tab['filesNumber']);
      });
  }

  checkFilterFile(searchValue) {
    return this.client
      .pause(1000)
      .then(() => {
        for (let k = 0; k < global.filesNumber; k++) {
          expect(global.fileLists[k]).to.contain(searchValue);
        }
      });
  }
}

module.exports = File;