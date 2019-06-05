/**
 * This script is based on the scenario described in this test link
 * [id="PS-144"][Name="Advanced"]
 **/

const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {Menu} = require('../../../../selectors/BO/menu.js');
const commonLocalization = require('../../../common_scenarios/localization');
const welcomeScenarios = require('../../../common_scenarios/welcome');

let firstAdvancedData = {
    languageIdentifier: 'fr',
    countryIdentifier: 'fr',
  },
  secondAdvancedData = {
    languageIdentifier: 'en',
    countryIdentifier: 'gb',
  },
  thirdAdvancedData = {
    languageIdentifier: 'en',
    countryIdentifier: 'fr',
  };

scenario('"Advanced"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  scenario('Access to "International > Localization" page', client => {
    test('should go to International > Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
  }, 'common_client');
  commonLocalization.updateAdvancedData(firstAdvancedData);
  commonLocalization.updateAdvancedData(secondAdvancedData);
  commonLocalization.updateAdvancedData(thirdAdvancedData);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
