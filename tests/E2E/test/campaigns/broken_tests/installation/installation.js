/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-82"][Name="Installation in language = country"]
 * [id="PS-83"][Name="Installation language <> country"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {Localization} = require('../../../selectors/BO/international/localization');
let promise = Promise.resolve();
const {Employee} = require('../../../selectors/BO/employee_page');
const {OnBoarding} = require('../../../selectors/BO/onboarding');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const welcomeScenarios = require('../../common_scenarios/welcome');
require('../../../globals.webdriverio.js');
require('../../install_upgrade/01_install.js');

/**
 * This script should be moved to the campaign installation when this issues will be fixed
 * https://github.com/PrestaShop/PrestaShop/issues/12168 && https://github.com/PrestaShop/PrestaShop/issues/10744
 **/
scenario('Check language, country and currency', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'installation');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Click on "Stop the OnBoarding" button', client => {
    test('should check and click on "Stop the OnBoarding" button', () => {
      return promise
        .then(() => client.isVisible(OnBoarding.stop_button))
        .then(() => client.stopOnBoarding(OnBoarding.stop_button));
    });
  }, 'onboarding');
  scenario('Check language, country and currency in the Back Office', client => {
    test('should go to "International > Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
    test('should close the symfony toolbar if exists', () => client.waitForSymfonyToolbar(AddProductPage, 2000));
    test('should set language from browser: "No"', () => client.waitForExistAndClick(Localization.Localization.language_browser_no_label));
    test('should set default country from browser: "No"', () => client.waitForExistAndClick(Localization.Localization.country_browser_no_label));
    test('should click on "Save" button', () => client.waitForExistAndClick(Localization.Localization.save_configuration_btn));
    test('should verify if the default language is "' + install_language.toUpperCase() + '"', () => client.checkDefaultConfiguration(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_default_language'), install_language, 'language'));

    /**
     * This scenario is broken when installation language is different to English.
     * The bug is described in this ticket
     * https://github.com/PrestaShop/PrestaShop/issues/12168
     **/
    test('should verify if the default country is "' + install_country.toUpperCase() + '"', () => client.checkDefaultConfiguration(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_default_country'), install_country, 'country'));
    test('should verify if the default currency is "' + country_currency.toUpperCase() + '"', () => client.checkDefaultConfiguration(Localization.Localization.configuration_selected_option.replace('%ID', 'form_configuration_default_currency'), country_currency, 'currency'));

  }, 'installation');

  scenario('Check language in the Front Office then check language in "Employee" page in the Back Office', client => {
    test('should click on "Shop name" then go to the Front Office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1));
    });
    test('should check the visibility of language list', () => client.isVisible(AccessPageFO.language_bloc));

    /**
     * This error is based on the bug described in this ticket
     * https://github.com/PrestaShop/PrestaShop/issues/10744
     **/
    test('should verify that the selected language is "' + install_language.toUpperCase() + '" if language and country of the installation are different', async () => {
      if (global.isVisible) {
        await client.isExisting(AccessPageFO.selected_language_by_isocode_option.replace("%ID", install_language), install_language);
      } else {
        await client.pause(0);
      }
    });
    test('should go back to the Back Office if language and country of the installation are different', async () => {
      if (global.isVisible) {
        await client.switchWindow(0);
      } else {
        await client.pause(0);
      }
    });
    test('should close the symfony toolbar if exists', () => client.waitForSymfonyToolbar(AddProductPage, 2000));
    test('should go to "Advanced Parameters > Team" page if language and country of the installation are different', async () => {
      if (global.isVisible) {
        await client.goToSubtabMenuPage(Menu.Configure.AdvancedParameters.advanced_parameters_menu, Menu.Configure.AdvancedParameters.team_submenu);
      } else {
        await client.pause(0);
      }
    });
    test('should click on "Edit" button if language and country of the installation are different', async () => {
      if (global.isVisible) {
        await client.waitForExistAndClick(Employee.edit_button);
      } else {
        await client.pause(0);
      }
    });
    test('should verify if the selected language is "' + install_language.toUpperCase() + '" if language and country of the installation are different', async () => {
      if (global.isVisible) {
        await client.checkEmployeeLanguage(Employee.selected_language_option, install_language);
      } else {
        await client.pause(0);
      }
    });
    test('should go back to the "Back Office"', () => client.switchWindow(0));
    test('should go to "International > Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
    test('should set language from browser: "Yes"', () => client.waitForExistAndClick(Localization.Localization.language_browser_yes_label));
    test('should set default country from browser: "Yes"', () => client.waitForExistAndClick(Localization.Localization.country_browser_yes_label));
    test('should click on "Save" button', () => client.waitForExistAndClick(Localization.Localization.save_configuration_btn));
  }, 'employee');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
