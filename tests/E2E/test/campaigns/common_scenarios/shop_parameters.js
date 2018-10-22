const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {ProductList} = require('../../selectors/BO/add_product_page');
const {ModulePage} = require('../../selectors/BO/module_page');
const {OnBoarding} = require('../../selectors/BO/onboarding.js');
const {Menu} = require('../../selectors/BO/menu.js');

let promise = Promise.resolve();

module.exports = {
  clickOnMenuLinksAndCheckElement: function (client, mainMenu, subMenu, pageSelector, describe1 = "", describe2 = "", pause = 0, tabMenu = "") {
    let page = describe2 === "" ? describe1 : describe2;
    if (mainMenu === "") {
      test('should click on "' + describe1 + '" menu', () => client.waitForExistAndClick(subMenu));
    } else {
      test('should click on "' + describe1 + '" menu', () => client.goToSubtabMenuPage(mainMenu, subMenu));
    }
    if (tabMenu !== "") {
      test('should click on "' + describe2 + '" tab', () => client.waitForExistAndClick(tabMenu));
    }
    test('should check that the "' + page + '" page is well opened', () => {
      return promise
        .then(() => client.waitForExist(pageSelector))
        .then(() => client.isExisting(pageSelector, pause));
    });
  },
  resetWelcomeModule: async function (client) {
    if (!global.startOnboarding) {
      await client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu);
      await client.waitForVisibleAndClick(Menu.Improve.Modules.installed_modules_tabs);
      await client.waitAndSetValue(ModulePage.modules_search_input, "welcome");
      await client.waitForExistAndClick(ModulePage.modules_search_button);
      await client.waitForVisibleAndClick(ModulePage.action_dropdown.replace('%moduleTechName', "welcome"));
      await client.waitForExistAndClick(ModulePage.reset_module.split('%moduleTechName').join("welcome"));
      await client.waitForVisibleAndClick(ModulePage.reset_button_modal.replace('%moduleTechName', "welcome"));
      await client.waitForExistAndClick(AddProductPage.close_validation_button);
      await client.waitForExistAndClick(Menu.dashboard_menu);
      await client.waitForExistAndClick(OnBoarding.resume_button, 3000);
    }
  }
};
