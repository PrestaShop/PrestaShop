const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {Translations} = require('../../../selectors/BO/international/translations');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');

scenario('Edit a translation', () => {
  scenario('Open the browser and connect to the BO', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in BO', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  scenario('Edit a translation of "Sign in" in the "classic Theme"', client => {
    test('should go to "Translations" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.translations_submenu));
    test('should select "themes translations" in the "MODIFY TRANSLATIONS" section', () => client.waitAndSelectByValue(Translations.translations_type, "themes"));
    test('should select the language "English (English)" in the "MODIFY TRANSLATIONS" section', () => client.waitAndSelectByValue(Translations.translations_language, "en"));
    test('should click on "Modify" button', () => client.waitForExistAndClick(Translations.modify_button));
    test('should click on "Shop" button', () => client.waitForVisibleAndClick(Translations.shop_button));
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
  scenario('Check the change of "Sign in" to "Sign in English" ', client => {
    test('should set the shop language to "English"', () => client.changeLanguage('english'));
    test('should check the "Sign in" button text ', () => client.checkTextValue(Translations.sign_out_FO_text, 'Sign out English', "contain"));
  }, 'common_client');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'common_client', true);
