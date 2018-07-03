var CommonClient = require('./common_client');
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

  getInstalledModulesNumber(ModulePage) {
    return this.client
      .getText(ModulePage.installed_module_span).then(function (text) {
        global.nbrModule = parseInt(text[0]);
      })
      .getText(ModulePage.built_in_module_span).then(function (text) {
        global.nbrBuilt = parseInt(text[0]);
      })
      .getText(ModulePage.theme_module_span).then(function (text) {
        global.nbrTheme = parseInt(text[0]);
      })
  }

  getModuleButtonName(ModulePage) {
    if (nbrModule === 1)
      return this.client.getText(ModulePage.action_module_installed_button).then(function (text) {
        buttonText = text.toUpperCase();
      });
    else if (nbrBuilt === 1)
      return this.client.getText(ModulePage.action_module_built_in_button).then(function (text) {
        buttonText = text.toUpperCase();
      });
    else return this.client.getText(ModulePage.action_module_theme_button).then(function (text) {
        buttonText = text.toUpperCase();
      })
  }

  clickOnConfigureModuleButton(ModulePage, moduleTechName) {
    if (buttonText === "CONFIGURE")
      return this.client
        .waitForExistAndClick(ModulePage.configure_module_button.split('%moduleTechName').join(moduleTechName));
    else return this.client
      .waitForExistAndClick(ModulePage.actions_module_dropdown)
      .waitForExistAndClick(ModulePage.configure_module_button.split('%moduleTechName').join(moduleTechName))
  }

  clickOnEnableModuleButton(ModulePage, moduleTechName) {
    if (buttonText === "ENABLE") {
      return this.client
        .waitForExistAndClick(ModulePage.enable_module.split('%moduleTechName').join(moduleTechName))
    } else if (buttonText === "DISABLE" || buttonText === "CONFIGURE")
      return this.client.pause(1000);
    else {
      return this.client
        .waitForExistAndClick(ModulePage.actions_module_dropdown)
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
        .waitForExistAndClick(ModulePage.actions_module_dropdown)
        .waitForExistAndClick(ModulePage.disable_module.split('%moduleTechName').join(moduleTechName), 3000)
    }
  }

}

module.exports = Module;
