const {AddProductPage} = require('../../selectors/BO/add_product_page');
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
      await client.waitForExistAndClick(AddProductPage.close_validation_button, 2000);
      await client.waitForExistAndClick(Menu.dashboard_menu, 3000);
      await client.waitForExistAndClick(OnBoarding.resume_button, 3000);
    }
  },
  async checkMboModule(client) {
    await client.pause(2000);
    await client.isVisible(OnBoarding.welcome_modal);
    if (isVisible) {
      await client.closeBoarding(OnBoarding.popup_close_button)
    }
    await client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_manager_submenu);
    await client.waitForVisibleAndClick(Menu.Improve.Modules.installed_modules_tabs);
    await client.waitAndSetValue(ModulePage.modules_search_input, "ps_mbo");
    await client.waitForExistAndClick(ModulePage.modules_search_button);
    await client.checkMboModule(ModulePage.installed_module_div.replace('%moduleTechName', "ps_mbo"));
  },
  async paymentSteps(client) {
    if (global.mboModule) {
      await client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '2'), 'class', 'id -done', 'equal');
      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'These payment methods are already available to your customers.', 'equal', 2000);
      await client.waitForExistAndClick(OnBoarding.payement_check_button.replace("%moduleTechName", "ps_checkpayment", 3000));
      await client.waitForVisibleAndClick(OnBoarding.resume_button, 2000);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button);

      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'And you can choose to add other payment methods from here!');
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button);
    } else {
      await client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '2'), 'class', 'id -done', 'equal');
      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/1', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'These payment methods are already available to your customers.', 'equal', 2000);
      await client.waitForExistAndClick(OnBoarding.payement_check_button.replace("%moduleTechName", "ps_checkpayment"));
      await client.waitForVisibleAndClick(OnBoarding.resume_button);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button);
    }
  },
  async carriersSteps(client) {
    if (global.mboModule) {
      await client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '3'), 'class', 'id -done', 'equal');
      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Here are the shipping methods available on your shop today.', 'equal', 2000);
      await client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button);

      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'You can offer more delivery options by setting up additional carriers');
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button);
    } else {
      await client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '3'), 'class', 'id -done', 'equal');
      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/1', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Here are the shipping methods available on your shop today.', 'equal', 2000);
      await client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button);
    }
  }
};
