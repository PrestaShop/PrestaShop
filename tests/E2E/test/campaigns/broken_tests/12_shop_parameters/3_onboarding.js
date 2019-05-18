/**
 * This script is based on the scenario described in this test link
 * [id="PS-28"][Name="Onboarding"]
 **/
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {OnBoarding} = require('../../../selectors/BO/onboarding.js');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const common = require('../../common_scenarios/shop_parameters');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const moduleCommonScenarios = require('../../common_scenarios/module');
const {ModulePage} = require('../../../selectors/BO/module_page');

/**
 * This script should be moved to the campaign full when this issue will be fixed
 * https://github.com/PrestaShop/PrestaShop/issues/12560
 **/
scenario('Welcome Module', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');

  scenario('Check then install "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'install');
  }, 'onboarding');

  scenario('Start tutorial', client => {
    test('should check "Resume" button or "Start" button', () => client.checkResumeAndStartButton(OnBoarding.start_button, OnBoarding.resume_button));
    test('should reset "Welcome" module', () => common.resetWelcomeModule(client));
  }, 'onboarding');

  scenario('The first tutorial step : Create the first product ', () => {
    common.firstStep(ProductList);
  }, 'common_client');

  /**
   * Related issue Here
   * https://github.com/PrestaShop/PrestaShop/issues/12560
   */
  scenario(' The second Tutorial step : Give the shop an own identity', () => {
    scenario('Step 1/2', client => {
      /**
       * Related issue Here
       * https://github.com/PrestaShop/PrestaShop/issues/12560
       */
      test('should check that the current step has started', () => client.checkAttributeValue(OnBoarding.welcomeSteps.tutorial_step.replace("%P", '1'), 'class', 'id -done', 'equal', 2000));
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button, 50, 2000));
      test('should close the symfony toolbar', () => client.waitForSymfonyToolbar(AddProductPage, 2000));
      test('should check that the step number is equal to "1" ', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '1/2', 'contain', 4000));
      test('should check the first onboarding-tooltip message', () => client.checkTextValue(OnBoarding.welcomeSteps.message_value, 'A good way to start is to add your own logo here!'));
      test('should check that the current page is "Theme catalog page"', () => client.isExisting(PagesForm.Design.configuration_fieldset));
      test('should upload the header logo', () => client.uploadPicture('image_test.jpg', OnBoarding.welcomeSteps.header_logo));
      test('should click on "Next" button', () => client.scrollWaitForExistAndClick(OnBoarding.welcomeSteps.next_button));
    }, 'common_client');

    scenario('Step 2/2', client => {
      test('should check that the step number is equal to "2"', () => client.checkTextValue(OnBoarding.welcomeSteps.tooltip_step, '2/2', 'contain', 4000));
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
  }, 'common_client');

  scenario('The third tutorial step : Get the shop ready for payments', client => {
    test('The third tutorial steps', async () => {
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
        await client.waitForExistAndClick(OnBoarding.welcomeSteps.next_button, 2000);
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
    });
  }, 'common_client');

  scenario('The fourth tutorial step : Choose the shipping solutions', client => {
    test('The fourth tutorial steps', () => common.carriersSteps(client));
  }, 'common_client');

  scenario('The fifth tutorial steps', () => {
    common.lastStep();
  }, 'common_client');

  scenario('Check then uninstall "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'Uninstall');
  }, 'onboarding');

  scenario('Start tutorial', client => {
    test('should click on "Resume" button or "Start" button', () => client.checkResumeAndStartButton(OnBoarding.start_button, OnBoarding.resume_button));
    test('should reset "Welcome" module', () =>
      common.resetWelcomeModule(client)
    );
  }, 'onboarding');

  scenario('The first tutorial step : Create the first product ', () => {
    common.firstStep(ProductList);
  }, 'common_client');

  scenario(' The second Tutorial step : Give the shop an own identity', () => {
    common.secondStep();
  }, 'common_client');

  scenario('The third tutorial step : Get the shop ready for payments', client => {
    test('The third tutorial steps', () => common.paymentSteps(client));
  }, 'common_client');

  scenario('The fourth tutorial step : Choose the shipping solutions', client => {
    test('The fourth tutorial steps', () => common.carriersSteps(client));
  }, 'common_client');

  scenario('The fifth tutorial steps', () => {
    common.lastStep();
  }, 'common_client');

  scenario('Back to default behaviour', client => {
    test('should go to the "Dashboard"', () => client.waitForExistAndClick(Menu.dashboard_menu, 2000));
    moduleCommonScenarios.uninstallModule(client, ModulePage, AddProductPage, "paypal");
  }, 'common_client');

  scenario('Check then install "ps_mbo" module', client => {
    moduleCommonScenarios.installUninstallMboModule(client, ModulePage, AddProductPage, "ps_mbo", 'install');
  }, 'onboarding');
}, 'common_client', true);

