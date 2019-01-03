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
let CommonClient = require('./common_client');
let buttonText;

global.moduleObject = {
  "data-name": '',
  "data-tech-name": '',
  "data-author": '',
  "data-description": '',
  "data-child-categories": '',
  "data-categories": '',
  "data-type": ''
};
global.moduleLists = {};
global.moduleInfo = [];
global.moduleSort = [];

class Module extends CommonClient {

  getModuleAttributes(selector, attr, index) {
    return this.client
      .getAttribute(selector.replace('%I', index), attr)
      .then((name) => {
        global.moduleLists[index - 1][attr] = name.toLowerCase();
      })
  }

  checkModuleData(number) {
    return this.client
      .pause(12000)
      .then(() => {
        for (let k = 0; k < number-1; k++) {
          var exist = false;
          for (var attr in moduleLists[k]) {
            if (moduleLists[k][attr].includes('contact') || moduleLists[k][attr].includes('form')) {
              exist = true;
            }
          }
          expect(exist).to.be.true;
        }
      })
  }

  getModuleAttr(selector, attr, index) {
    return this.client
      .getAttribute(selector.replace('%I', index + 1), attr)
      .then((name) => {
        moduleSort[index] = name.toLowerCase();
        moduleInfo[index] = name.toLowerCase();
      })
  }

  checkSortByName(length) {
    return this.client
      .pause(1000)
      .then(() => {
        this.client
          .waitUntil(function () {
            expect(moduleInfo.sort()).to.deep.equal(moduleSort)
          }, 1000 * length)
      });
  }

  checkSortByIncPrice(length) {
    return this.client
      .pause(1000)
      .then(() => {
        this.client
          .waitUntil(function () {
            expect(moduleInfo.sort(function (a, b) {
              return a - b;
            })).to.deep.equal(moduleSort)
          }, 1000 * length)
      });
  }

  checkSortDesc(length) {
    return this.client
      .pause(1000)
      .then(() => {
        this.client
          .waitUntil(function () {
            expect(moduleInfo.sort(function (a, b) {
              return a - b;
            }).reverse()).to.deep.equal(moduleSort)
          }, 1000 * length)
      });
  }
    getModuleButtonName(ModulePage, moduleTechName) {
            return this.client.getText(ModulePage.module_action_href.split('%moduleTechName').join(moduleTechName)).then(function (text) {
                buttonText = text.toUpperCase();
            });
    }
    clickOnConfigureModuleButton(ModulePage, moduleTechName) {
        if (buttonText === "CONFIGURE")
            return this.client
                .waitForExistAndClick(ModulePage.configure_link.replace('%moduleTechName', moduleTechName));
        else return this.client
            .waitForExistAndClick(ModulePage.action_dropdown.replace('%moduleTechName', moduleTechName))
            .waitForExistAndClick(ModulePage.configure_link.replace('%moduleTechName', moduleTechName))
    }
    clickOnEnableModuleButton(ModulePage, moduleTechName) {
        if (buttonText === "ENABLE") {
            return this.client
                .waitForExistAndClick(ModulePage.enable_module.split('%moduleTechName').join(moduleTechName),2000)
        } else if (buttonText === "DISABLE" || buttonText === "CONFIGURE")
            return this.client.pause(1000);
        else {
            return this.client
                .waitForExistAndClick(ModulePage.action_dropdown.replace('%moduleTechName', moduleTechName),2000)
                .waitForExistAndClick(ModulePage.enable_module.split('%moduleTechName').join(moduleTechName), 3000)
        }
    }
    clickOnDisableModuleButton(ModulePage, moduleTechName) {
        if (buttonText === "DISABLE") {
            return this.client
                .waitForExistAndClick(ModulePage.disable_module.split('%moduleTechName').join(moduleTechName))
        }
        else if (buttonText === "ENABLE")
            return this.client.pause(1000);
        else {
            return this.client
                .waitForExistAndClick(ModulePage.action_dropdown.replace('%moduleTechName', moduleTechName))
                .waitForExistAndClick(ModulePage.disable_module.split('%moduleTechName').join(moduleTechName), 3000)
        }
    }
}

module.exports = Module;
