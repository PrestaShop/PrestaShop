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

  /**
   * Related issue Here
   * https://github.com/PrestaShop/PrestaShop/issues/13569
   */

  scenario('The first tutorial step : Create the first product ', () => {
    common.firstStep(ProductList);
  }, 'common_client');

  /**
   * Related issue Here
   * https://github.com/PrestaShop/PrestaShop/issues/12560
   */
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

