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
const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {Translations} = require('../../../selectors/BO/international/translations');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../common_scenarios/welcome');

let promise = Promise.resolve();
scenario('Edit a translation', () => {
  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Edit a translation of "Sign in" in the "classic Theme"', client => {
    test('should go to "Translations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.translations_submenu));
    test('should select "themes translations" in the "MODIFY TRANSLATIONS" section', () => client.waitAndSelectByValue(Translations.translations_type, "themes"));
    test('should select the language "English (English)" in the "MODIFY TRANSLATIONS" section', () => client.waitAndSelectByValue(Translations.translations_language, "en"));
    test('should click on "Modify" button', () => client.waitForExistAndClick(Translations.modify_button));
    test('should click on "Shop" button', () => {
      return promise
        .then(() => {
          if (global.ps_mode_dev) {
            client.waitForExistAndClick(AddProductPage.symfony_toolbar)
          }
        })
        .then(() => client.waitForVisibleAndClick(Translations.shop_button));
    });
    test('should click on "Theme" button', () => client.waitForVisibleAndClick(Translations.theme_button));
    test('should click on "Action" button', () => client.waitForVisibleAndClick(Translations.action_button));
    test('should change "Sign Out" translation from "Sign Out" to "Sign Out English"', () => client.waitAndSetValue(Translations.Sign_out_textarea_button, "Sign out English"));
  }, 'common_client');
  scenario('Save change', client => {
    test('should click on "Save" button ', () => client.scrollWaitForExistAndClick(Translations.save_button));
    test('should check that the success alert message is well displayed', () => client.waitForExistAndClick(AddProductPage.close_validation_button));
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
  scenario('Login in the Front Office', client => {
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'common_client');
  scenario('Check the change of "Sign out" to "Sign out English" ', client => {
    test('should set the shop language to "English"', () => client.changeLanguage());
    test('should check the "Sign out" button text is equal to "Sign out English"', () => client.checkTextValue(Translations.sign_out_FO_text, 'Sign out English', "contain", 2000));
  }, 'common_client');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'common_client', true);
