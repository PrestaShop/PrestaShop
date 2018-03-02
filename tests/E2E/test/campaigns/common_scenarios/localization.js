const {Localization} = require('../../selectors/BO/international/localization');
const {InternationalPage} = require('../../selectors/BO/international/index');
const {Menu} = require('../../selectors/BO/menu.js');

module.exports = {
  createLanguage: function (languageData)  {
    scenario('Create a new "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should click on "Add new language" button', () => client.waitForExistAndClick(Localization.languages.add_new_language_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Localization.languages.name_input, languageData.name + date_time));
      test('should set the "ISO code" input', () => client.waitAndSetValue(Localization.languages.iso_code_input, languageData.iso_code));
      test('should set the "Language code" input', () => client.waitAndSetValue(Localization.languages.language_code_input, languageData.language_code));
      test('should upload the picture', () => client.uploadPicture(languageData.flag_file, Localization.languages.flag_file, 'flag'));
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.languages.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful creation.'));
    }, 'common_client');
  },
  editLanguage: function (name, languageData)  {
    scenario('Edit the created "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should search for the created language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, name + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Localization.languages.edit_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Localization.languages.name_input, languageData.name + date_time));
      test('should set the "ISO code" input', () => client.waitAndSetValue(Localization.languages.iso_code_input, languageData.iso_code));
      test('should set the "Language code" input', () => client.waitAndSetValue(Localization.languages.language_code_input, languageData.language_code));
      test('should upload the picture', () => client.uploadPicture(languageData.flag_file, Localization.languages.flag_file, 'flag'));
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.languages.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful update.'));
    }, 'common_client');
  },
  checkLanguage: function (languageData)  {
    scenario('Check the created "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should search for the created language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, languageData.name + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Localization.languages.edit_button));
      test('should check the "Name" value of the language', () => client.checkAttributeValue(Localization.languages.name_input, 'value', languageData.name + date_time));
      test('should check the "ISO code" value of the language', () => client.checkAttributeValue(Localization.languages.iso_code_input, 'value', languageData.iso_code.toLowerCase()));
      test('should check the "Language code" value of the language', () => client.checkAttributeValue(Localization.languages.language_code_input, 'value', languageData.language_code));
    }, 'common_client');
  },
  deleteLanguage: function (name)  {
    scenario('Delete the created"Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should search for the created language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, name + date_time));
      test('should click on "dropdown toggle" button', () => client.waitForExistAndClick(Localization.languages.dropdown_button));
      test('should click on "Delete" button', () => client.waitForExistAndClick(Localization.languages.delete_button));
      test('should accept the confirmation alert', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful deletion.'));
    }, 'common_client');
  },
};