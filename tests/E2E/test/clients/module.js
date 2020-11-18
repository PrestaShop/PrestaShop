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
global.moduleCategoryNumber = 0;

class Module extends CommonClient {

  getModuleAttributes(selector, attr, index) {
    return this.client
      .getAttribute(selector.replace('%I', index), attr)
      .then((name) => {
        global.moduleLists[index - 1][attr] = name.toLowerCase();
      })
  }

  getModuleField(selector, attr, index, sorted = false) {
    return this.client
      .getAttribute(selector.replace('%I', index + 1), attr)
      .then((name) => {
        if (sorted) {
          moduleSort[index] = name.toLowerCase();
        } else {
          moduleInfo[index] = name.toLowerCase();
        }
      });
  }

  getModulePrice(selector, attr, index, sorted = false) {
    return this.client
      .getText(selector.replace('%I', index + 1), attr)
      .then((name) => {
        if (sorted) {
          if (name === 'Free') {
            moduleSort[index] = '0';
          } else {
            moduleSort[index] = name.replace(' ', '').replace('€', '').toLowerCase();
          }
        } else {
          if (name === 'Free') {
            moduleInfo[index] = '0';
          } else {
            moduleInfo[index] = name.replace(' ', '').replace('€', '').toLowerCase();
          }
        }
      });
  }

  getModuleButtonName(ModulePage, moduleTechName, selector = ModulePage.module_action_href) {
    return this.client.getText(selector.split('%moduleTechName').join(moduleTechName)).then(function (text) {
      buttonText = text.toString().toUpperCase();
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
        .waitForExistAndClick(ModulePage.enable_module.split('%moduleTechName').join(moduleTechName), 2000)
    } else if (buttonText === "DISABLE" || buttonText === "CONFIGURE")
      return this.client.pause(1000);
    else {
      return this.client
        .waitForExistAndClick(ModulePage.action_dropdown.replace('%moduleTechName', moduleTechName), 2000)
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

  getModuleNumber(selector, globalVar, timeout = 90000) {
    return this.client
      .waitForExist(selector, timeout)
      .then(() => this.client.getText(selector))
      .then((variable) => global.tab[globalVar] = variable.split(' ')[0]);
  }

  checkNumberModule(selector, textToCheckWith) {
    return this.client
      .waitForExist(selector, 9000)
      .then(() => this.client.getText(selector))
      .then((text) => expect(text.split(' ')[0]).to.equal(textToCheckWith));
  }

  async checkSortModule(isNumber = false, increasing = true) {
    return await this.client
      .pause(2000)
      .then(async () => {
        if (isNumber) {
          if (increasing) {
            await expect(moduleInfo.sort(function (a, b) {
              return a - b;
            })).to.deep.equal(moduleSort);
          } else {
            await expect(moduleInfo.sort(function (a, b) {
              return a - b
            }).reverse()).to.deep.equal(moduleSort);
          }
        } else {
          if (increasing) {
            await expect(moduleInfo.sort()).to.deep.equal(moduleSort);
          } else {
            await expect(moduleInfo.sort().reverse()).to.deep.equal(moduleSort);
          }
        }
      });
  }

  async getCategoryNumber(selector, pause = 0) {
    return this.client
      .pause(pause)
      .execute(function (selector) {
        return document.querySelector(selector).childElementCount;
      }, selector)
      .then((count) => {
        global.moduleCategoryNumber = count.value - 2;
      })
  }

  async checkModuleNumberByCategory(selector, moduleNumber, pause = 0) {
    return this.client
      .pause(pause)
      .execute(function (selector) {
        return document.querySelector(selector).childElementCount;
      }, selector)
      .then((count) => {
        expect(count.value.toString()).to.equal(moduleNumber);
      })
  }
}

module.exports = Module;
