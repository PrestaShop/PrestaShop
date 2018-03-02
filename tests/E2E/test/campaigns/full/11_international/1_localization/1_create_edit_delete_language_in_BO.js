const {AccessPageBO} = require('../../../../selectors/BO/access_page');
const common_scenarios = require('../../../common_scenarios/localization');

let languageData = {
  name: 'SuisseLanguage',
  iso_code: 'CH',
  language_code: 'ch',
  flag_file: 'language_suisse_flag.jpg'
}, languageEditedData = {
  name: 'SuisseLanguageUpdate',
  iso_code: 'HC',
  language_code: 'hc',
  flag_file: 'language_suisse_flag.jpg'
};

scenario('Create, edit, delete and check "Languages" in the Back Office', () => {
  scenario('Open the browser and connect to the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should log in successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  common_scenarios.createLanguage(languageData);
  common_scenarios.checkLanguage(languageData);
  common_scenarios.editLanguage(languageData.name, languageEditedData);
  common_scenarios.checkLanguage(languageEditedData);
  common_scenarios.deleteLanguage(languageEditedData.name);
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'common_client');
}, 'common_client', true);