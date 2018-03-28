const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu');
const {ModulePage} = require('../../../selectors/BO/module_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const module_common_scenarios = require('../../common_scenarios/module');
let promise = Promise.resolve();

scenario('Check notification module in the Back Office', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Configure "Bank Transfer" module', client => {
    test('should go to "Modules" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_services_submenu));
    test('should click on "Notifications" tab', () => {
      return promise
        .then(() => client.getTextInVar(ModulePage.notification_number, 'notification'))
        .then(() => client.waitForExistAndClick(Menu.Improve.Modules.notifications_tabs));
    });
    test('should click on "Configure" button', () => client.waitForExistAndClick(ModulePage.configure_module.replace('%moduleTechName', 'ps_wirepayment')));
    test('should set the "Account owner" input', () => client.waitAndSetValue(ModulePage.ModuleBankTransferPage.account_owner_input, 'Demo'));
    test('should set the "Account details" textarea', () => client.waitAndSetValue(ModulePage.ModuleBankTransferPage.account_details_textarea, 'Check notification module'));
    test('should set the "Bank address" textarea', () => client.waitAndSetValue(ModulePage.ModuleBankTransferPage.bank_address_textarea, 'Boulvard street n°9 - 70501'));
    test('should click on "Save" button', () => client.waitForExistAndClick(ModulePage.ModuleBankTransferPage.save_button));
  }, 'common_client');
  scenario('Check that the module is well configured', client => {
    test('should go to "Modules" page', () => client.goToSubtabMenuPage(Menu.Improve.Modules.modules_menu, Menu.Improve.Modules.modules_services_submenu));
    test('should click on "Notifications" tab', () => client.waitForExistAndClick(Menu.Improve.Modules.notifications_tabs));
    test('should check that the "Notifications number" is decremented with 1', () => client.checkTextValue(ModulePage.notification_number, (tab['notification'] - 1).toString() ));
    test('should check that the configured module is not visible in the "Notifications" tab', () => client.checkIsNotVisible(ModulePage.configure_module.replace('%moduleTechName', 'ps_wirepayment')));
  }, 'common_client');
  scenario('Reset the configured module', client => {
    module_common_scenarios.resetModule(client, ModulePage, AddProductPage, Menu, 'Bank transfer', 'ps_wirepayment');
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);