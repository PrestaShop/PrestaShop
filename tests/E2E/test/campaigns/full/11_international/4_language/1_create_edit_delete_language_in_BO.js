/**
 * This script is based on scenarios described in this combination of the following tests link
 * [id="PS-145"][Name="Add a language"]
 * [id="PS-146"][Name="Edit a language"]
 * [id="PS-147"][Name="Delete a language"]
 **/

const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../../selectors/FO/access_page');
const common_scenarios = require('../../../common_scenarios/localization');
const welcomeScenarios = require('../../../common_scenarios/welcome');

let languageData = [{
  name: 'Spanish',
  iso_code: 'ES',
  language_code: 'ES',
  date_format: 'Y-m-d',
  date_format_full: 'Y-m-d H:i:s',
  flag_file: 'language_spanish_flag.png',
  no_picture_file: 'no_image_available.png',
  is_rtl: '0',
  status: '1'
}, {
  name: 'Farsi',
  iso_code: 'FA',
  language_code: 'FA',
  date_format: 'Y-m-d',
  date_format_full: 'Y-m-d H:i:s',
  flag_file: 'language_farsi_flag.png',
  no_picture_file: 'no_image_available.png',
  is_rtl: '1',
  status: '1'
}], languageEditedData = [{
  name: 'Spanishh',
  iso_code: 'ES',
  language_code: 'ES',
  date_format: 'Y-m-d',
  date_format_full: 'Y-m-d H:i:s',
  flag_file: 'language_spanish_flag.png',
  no_picture_file: 'no_image_available.png',
  is_rtl: '0',
  status: '1'
}, {
  name: 'Spanishh',
  iso_code: 'ES',
  language_code: 'ES',
  date_format: 'Y-m-d',
  date_format_full: 'Y-m-d H:i:s',
  flag_file: 'language_spanish_flag.png',
  no_picture_file: 'no_image_available.png',
  is_rtl: '0',
  status: '0'
}];

/**
 * This script should be moved to the campaign full when this issue will be fixed
 * https://github.com/PrestaShop/PrestaShop/issues/9719
 **/
scenario('Create, edit, delete and check "Languages" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();

  scenario('Test 1: Create, check language in the Back Office and check it in the Front Office', () => {
    common_scenarios.createLanguage(languageData[0]);
    common_scenarios.checkLanguageBO(languageData[0]);
    scenario('Go to the Front Office', client => {
      test('should go to the Front Office', async () => {
        await client.pause(500);
        await client.accessToFO(AccessPageFO);
      });
    }, 'common_client');
    common_scenarios.checkLanguageFO(languageData[0]);
  }, 'common_client');

  scenario('Test 2: Create, check language in the Back Office and check it in the Front Office', () => {
    scenario('Go back to the Back Office', client => {
      test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
    }, 'common_client');
    common_scenarios.createLanguage(languageData[1]);
    common_scenarios.checkLanguageBO(languageData[1]);
    common_scenarios.generateRtlStylesheet();
    scenario('Go to the Front Office', client => {
      test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
    }, 'common_client');
    common_scenarios.checkLanguageFO(languageData[1]);
  }, 'common_client');

  scenario('Test 3: Edit the created language in the Back Office and check it in the Front Office', () => {
    scenario('Go back to the Back Office', client => {
      test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
    }, 'common_client');
    common_scenarios.editLanguage(languageData[0].name, languageEditedData[0]);
    scenario('Go to the Front Office', client => {
      test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
    }, 'common_client');
    common_scenarios.checkLanguageFO(languageEditedData[0]);
    scenario('Go back to the Back Office', client => {
      test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
    }, 'common_client');
    common_scenarios.editLanguage(languageEditedData[0].name, languageEditedData[1]);
    scenario('Go to the Front Office', client => {
      test('should go to the Front Office', () => client.accessToFO(AccessPageFO));
    }, 'common_client');
    common_scenarios.checkLanguageFO(languageData[0], true);
  }, 'common_client');

  scenario('Test 4: Delete the created languages in the Back Office and check it in the Front Office', () => {
    scenario('Go back to the Back Office', client => {
      test('should go back to the Back Office', () => client.accessToBO(AccessPageBO));
    }, 'common_client');
    common_scenarios.deleteLanguage(languageData[1].name);
    common_scenarios.deleteLanguage(languageEditedData[1].name);
    scenario('Go to the Front Office', client => {
      test('should go to the Front Office', async () => {
        await client.pause(500);
        await client.accessToFO(AccessPageFO);
      });
    }, 'common_client');
    common_scenarios.checkLanguageFO(languageData[1], true);
  }, 'common_client');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);
