const {AddProductPage} = require('../../selectors/BO/add_product_page');
const {ModulePage} = require('../../selectors/BO/module_page');
const {OnBoarding} = require('../../selectors/BO/onboarding.js');
const {Menu} = require('../../selectors/BO/menu.js');
const {PagesForm} = require('../../selectors/BO/pages_form');
const {ModulesCatalogPage} = require('../../selectors/BO/addons_catalog_page');

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
      await client.waitForExistAndClick(ModulePage.action_dropdown.replace('%moduleTechName', "welcome"), 1000);
      await client.waitForExistAndClick(ModulePage.reset_module.split('%moduleTechName').join("welcome"), 2000);
      await client.waitForExistAndClick(ModulePage.reset_button_modal.replace('%moduleTechName', "welcome"), 1000);
      await client.waitForExistAndClick(AddProductPage.close_validation_button, 3000);
      await client.waitForExistAndClick(Menu.dashboard_menu, 3000);
    }
  },
  async checkMboModule(client) {
    await client.pause(2000);
    await client.isVisible(OnBoarding.welcome_modal);
    if (global.isVisible) {
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
      await client.isExisting(PagesForm.Payment.active_payment);
      await this.configWirePaymentModule(client);
      await client.waitForVisibleAndClick(OnBoarding.resume_button, 2000);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button);
      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'And you can choose to add other payment methods from here!');
      await client.waitForExistAndClick(OnBoarding.install_paypal_button, 2000);
      await client.isExisting(OnBoarding.paypal_conf_page, 1000);
      await client.waitForVisibleAndClick(OnBoarding.resume_button, 2000);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 1000);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.continue_button, 2000);
    } else {
      await client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '2'), 'class', 'id -done', 'equal');
      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/1', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'These payment methods are already available to your customers.', 'equal', 2000);
      await client.isExisting(PagesForm.Payment.active_payment);
      await this.configWirePaymentModule(client);
      await client.waitForVisibleAndClick(OnBoarding.resume_button);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button);
    }
  },
  async carriersSteps(client) {
    if (global.mboModule) {
      await client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '3'), 'class', 'id -done', 'equal', 2000);
      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Here are the shipping methods available on your shop today.', 'equal', 2000);
      await client.isExisting(PagesForm.Shipping.carrier_form);
      await client.waitForExistAndClick(OnBoarding.edit_carrier_button);
      await client.waitForExistAndClick(OnBoarding.resume_button, 8000);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 5000);
      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'You can offer more delivery options by setting up additional carriers');
      await client.waitForExistAndClick(OnBoarding.install_chronopost_button);
      /**
       * Related issue Here
       * https://github.com/PrestaShop/PrestaShop/issues/11388
       * We can not install the module chronopost, we are redirected to the module page which is a wrong behaviour
       */
      await client.waitForExistAndClick(OnBoarding.resume_button, 5000);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 2000);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 2000);
    } else {
      await client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '3'), 'class', 'id -done', 'equal');
      await client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/1', 'contain', 4000);
      await client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Here are the shipping methods available on your shop today.', 'equal', 2000);
      await client.isExisting(PagesForm.Shipping.carrier_form);
      await client.waitForExistAndClick(OnBoarding.edit_carrier_button);
      await client.waitForExistAndClick(OnBoarding.resume_button, 8000);
      await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 5000);
    }
  },
  enablePrestashopDebugMode: function (Menu, Performance) {
    if (global.ps_mode_dev === false) {
      scenario('Enable the debug mode', client => {
        test('should go to "Performance" page', () => client.goToSubtabMenuPage(Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.performance_submenu));
        test('should switch the "Debug mode" to "Yes"', () => client.waitForExistAndClick(Performance.enableDebugMode));
        test('should click on "Save" button', async () => {
          await client.waitForSymfonyToolbar(AddProductPage, 2000);
          await client.waitForExistAndClick(Performance.save_button.replace('%I', 2));
        });
        test('should verify the appearance of the green validation', () => client.checkTextValue(Performance.success_box, "Successful update."));
      }, 'common_client');
    }
  },
  async verifyWelcomingModule(client) {
    await client.isVisible(OnBoarding.start_button, 1000);
    if (global.isVisible) {
      await client.waitForExistAndClick(OnBoarding.later_button, 2000);
      await client.isNotExisting(OnBoarding.welcome_modal, 1000);
      await client.waitForExistAndClick(OnBoarding.resume_button, 2000);
      await client.isExisting(OnBoarding.welcome_modal, 1000);
      await client.waitForExistAndClick(OnBoarding.start_button, 5000)
    } else {
      await client.waitForExistAndClick(Menu.dashboard_menu, 3000);
      await client.waitForExistAndClick(OnBoarding.resume_button, 2000);
      await client.isExisting(OnBoarding.welcome_modal, 1000);
      await client.waitForExistAndClick(OnBoarding.later_button, 1000);
      await client.isNotExisting(OnBoarding.welcome_modal, 1000);
      await client.waitForExistAndClick(OnBoarding.resume_button, 1000);
      await client.isExisting(OnBoarding.welcome_modal, 1000);
      await client.waitForExistAndClick(OnBoarding.start_button, 4000);
    }
  },
  firstStep(ProductList) {
    scenario('Step 1/5', client => {
      test('should click on "Start" or "Resume" button', () => this.verifyWelcomingModule(client));
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '0'), 'class', 'id -done', 'equal', 5000));
      test('should check the existence of the onboarding-tooltip', () => client.isExisting(OnBoarding.welcomeSteps.onboarding_tooltip, 4000));
      test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Give your product a catchy name.'));
      test('should check that the step number is equal to "1"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/5'));
      test('should set the "Product name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, 'productTest' + date_time));
    }, 'common_client');

    scenario('Step 2/5', client => {
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 2000));
      test('should check that the step number is equal to "2"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2', 'contain', 2000));
      test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Fill out the essential details in this tab. The other tabs are for more advanced information.'));
    }, 'common_client');

    scenario('Step 3/5 ', client => {
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 2000));
      test('should check that the step number is equal to "3"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '3', 'contain', 2000));
      test('should check the third  onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Add one or more pictures so your product looks tempting!'));
      test('should upload the picture of product', () => client.uploadPicture('image_test.jpg', AddProductPage.picture));
    }, 'common_client');

    scenario('Step 4/5', client => {
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "4"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '4', 'contain', 4000));
      test('should check the fourth  onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'How much do you want to sell it for?', 'equal', 2000));
      test('should set the "Tax exclude" price', () => {
        return promise
          .then(() => client.scrollTo(AddProductPage.priceTE_shortcut, 50))
          .then(() => client.waitAndSetValue(AddProductPage.priceTE_shortcut, '50'));
      });
    }, 'common_client');

    scenario('Step 5/5', client => {
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
      test('should check that the step number is equal to "5"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '5', 'contain', 5000));
      test('should check the fifth onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Yay! You just created your first product. Looks good, right?', 'equal', 2000));
      test('should search for the product"' + 'productTest' + date_time + '"', () => {
        return promise
          .then(() => client.waitAndSetValue(AddProductPage.catalogue_filter_by_name_input, 'productTest' + date_time))
          .then(() => client.waitForExistAndClick(AddProductPage.catalogue_submit_filter_button));
      });
      test('should check the product', () => client.checkTextValue(ProductList.product_name.replace("%ID", '1'), 'productTest' + date_time));
      test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'common_client');
  },


  async secondStep() {
    scenario('Step 1/2', client => {
      /**
       * Related issue Here
       * https://github.com/PrestaShop/PrestaShop/issues/12560
       */
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '1'), 'class', 'id -done', 'equal', 2000));
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button, 50, 2000));
      test('should close the symfony toolbar', () => client.waitForSymfonyToolbar(AddProductPage, 2000));
      test('should click on "Continue" button not on "Next Button" because of issue here #12560', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.continue_button, 6000));
    }, 'common_client');

    scenario('Step 2/2', client => {
      test('should check the second onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'If you want something really special, have a look at the theme catalog!', 'equal', 3000));
      test('should check that the current page is "Theme catalog page"', () => client.isExisting(OnBoarding.welcomeSteps.discover_button, 2000));
      test('should click on "Discover" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.discover_button));
      test('should check that the  page is well opened', async () => {
        await client.switchWindow(1);
        await client.isExisting(ModulesCatalogPage.prestashop_addons_logo, 2000);
        await client.switchWindow(0);
      });
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button));
    }, 'common_client');
  },
  lastStep() {
    scenario('Step 1/2 : Discover the module selection', client => {
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '4'), 'class', 'id -done', 'equal', 1000));
      test('should check that the page is "Module" page', () => client.isExisting(PagesForm.Modules.modules_list, 2000));
      test('should check that the step number is equal to "1"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 4000));
      test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'Discover our module selection in the first tab. Manage your modules on the second one and be aware of notifications in the third tab.', 'equal', 2000));
      test('should click on "Next" button', () => client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 4000));
    }, 'common_client');
    scenario('Step 2/2 : Get the shop ready for payments', client => {
      test('should verify if the modal "Over to you" is opened', () => client.isExisting(OnBoarding.over_to_you_modal, 2000));
      test('should click on "Ready" button', () => client.waitForExistAndClick(OnBoarding.ready_button, 1000));
      test('should verify if the modal "Over to you" is closed', () => client.isNotExisting(OnBoarding.welcome_modal));
    }, 'common_client');
  },
  async configWirePaymentModule(client) {
    await client.waitForExistAndClick(OnBoarding.banktransfer_check_button.replace("%moduleTechName", "ps_wirepayment", 3000));
    await client.waitAndSetValue(OnBoarding.banktransfer_accountowner_input, "Account owner");
    await client.waitAndSetValue(OnBoarding.banktransfer_accountdetails_input, "Account details");
    await client.waitAndSetValue(OnBoarding.banktransfer_bankaddress_input, "Bank address");
    await client.waitForVisibleAndClick(OnBoarding.banktransfer_save_button, 2000);
    await client.checkTextValue(OnBoarding.success_alert, "Settings updated", "contain", 2000);
  }
};

