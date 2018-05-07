const {Localization} = require('../../selectors/BO/international/localization');
const {InternationalPage} = require('../../selectors/BO/international/index');
const {Menu} = require('../../selectors/BO/menu.js');
const {ThemeAndLogo} = require('../../selectors/BO/design/theme_and_logo');
const Design = require('../../selectors/BO/design/index');
const {languageFO} = require('../../selectors/FO/index');

module.exports = {
  createLanguage: function (languageData)  {
    scenario('Create a new "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should click on "Add new language" button', () => client.waitForExistAndClick(Localization.languages.add_new_language_button));
      test('should set the "Name" input', () => client.waitAndSetValue(Localization.languages.name_input, languageData.name + date_time));
      test('should set the "ISO code" input', () => client.waitAndSetValue(Localization.languages.iso_code_input, languageData.iso_code));
      test('should set the "Language code" input', () => client.waitAndSetValue(Localization.languages.language_code_input, languageData.language_code));
      test('should set the "Date format" input', () => client.waitAndSetValue(Localization.languages.date_format_input, languageData.date_format));
      test('should set the "Date format (full)" input', () => client.waitAndSetValue(Localization.languages.date_format_full_input, languageData.date_format_full));
      test('should upload the "Flag" picture', () => client.uploadPicture(languageData.flag_file, Localization.languages.flag_file, 'flag'));
      test('should upload the "No-picture" image', () => client.uploadPicture(languageData.no_picture_file, Localization.languages.no_picture_file, 'no_picture'));
      test('should switch the "Is RTL language"', () => client.waitForExistAndClick(Localization.languages.is_rtl_button.replace('%S', languageData.is_rtl)));
      test('should switch the "Status"', () => client.waitForExistAndClick(Localization.languages.status_button.replace('%S', languageData.status)));
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
      test('should set the "Date format" input', () => client.waitAndSetValue(Localization.languages.date_format_input, languageData.date_format));
      test('should set the "Date format (full)" input', () => client.waitAndSetValue(Localization.languages.date_format_full_input, languageData.date_format_full));
      test('should upload the "Flag" picture', () => client.uploadPicture(languageData.flag_file, Localization.languages.flag_file, 'flag'));
      test('should upload the "No-picture" image', () => client.uploadPicture(languageData.no_picture_file, Localization.languages.no_picture_file, 'no_picture'));
      if (languageData.hasOwnProperty('is_rtl')) {
        test('should switch the "Is RTL language"', () => client.waitForExistAndClick(Localization.languages.is_rtl_button.replace('%S', languageData.is_rtl)));
      }
      if (languageData.hasOwnProperty('status')) {
        test('should switch the "Status"', () => client.waitForExistAndClick(Localization.languages.status_button.replace('%S', languageData.status)));
      }
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.languages.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful update.'));
    }, 'common_client');
  },
  checkLanguageBO: function (languageData)  {
    scenario('Check the created "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should search for the created language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, languageData.name + date_time));
      test('should click on "Edit" button', () => client.waitForExistAndClick(Localization.languages.edit_button));
      test('should check that the "Name" is equal to "' + languageData.name + date_time + '"', () => client.checkAttributeValue(Localization.languages.name_input, 'value', languageData.name + date_time));
      test('should check that the "ISO code" is equal to "' + languageData.iso_code.toLowerCase() + '"', () => client.checkAttributeValue(Localization.languages.iso_code_input, 'value', languageData.iso_code.toLowerCase()));
      test('should check that the "Language code" is equal to "' + languageData.language_code + '"', () => client.checkAttributeValue(Localization.languages.language_code_input, 'value', languageData.language_code));
      test('should check that the "Date format" is equal to "' + languageData.date_format + '"', () => client.checkAttributeValue(Localization.languages.date_format_input, 'value', languageData.date_format));
      test('should check that the "Date format (full)" is equal to "' + languageData.date_format_full + '"', () => client.checkAttributeValue(Localization.languages.date_format_full_input, 'value', languageData.date_format_full));
      test('should click on "Save" button', () => client.waitForExistAndClick(Localization.languages.save_button));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Localization.languages.reset_button));
    }, 'common_client');
  },
  checkLanguageFO: function (languageData, isDeleted = false)  {
    scenario('Check the created "Language" in the Front Office', client => {
      if (isDeleted) {
        test('should click on "Language" select', () => client.waitForExistAndClick(languageFO.language_selector));
        test('should check that the "' + languageData.name + '" doesn\'t appear', () => client.checkIsNotVisible(languageFO.language_option.replace('%LANG', languageData.iso_code.toLowerCase())));
      } else {
        test('should set the shop language to "' + languageData.name + '"', () => client.changeLanguage(languageData.iso_code.toLowerCase()));
        test('should check that the "' + languageData.name + '" language is well selected', () => client.checkTextValue(languageFO.selected_language_button, languageData.name + date_time));
      }
    }, 'common_client');
  },
  deleteLanguage: function (name)  {
    scenario('Delete the created "Language"', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Languages" tab', () => client.waitForExistAndClick(Menu.Improve.International.languages_tab));
      test('should search for the created language', () => client.searchByValue(Localization.languages.filter_name_input, Localization.languages.filter_search_button, name + date_time));
      test('should click on "dropdown toggle" button', () => client.waitForExistAndClick(Localization.languages.dropdown_button));
      test('should click on "Delete" button', () => client.waitForExistAndClick(Localization.languages.delete_button));
      test('should accept the confirmation alert', () => client.alertAccept());
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, '×\nSuccessful deletion.'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(Localization.languages.reset_button));
    }, 'common_client');
  },
  generateRtlStylesheet: function () {
    scenario('Generate RTL stylesheet', client => {
      test('should go to "Theme & logo" page', () => client.goToSubtabMenuPage(Menu.Improve.Design.design_menu, Menu.Improve.Design.theme_logo_submenu));
      test('should switch the "Generate RTL stylesheet" to "YES"', () => client.scrollWaitForExistAndClick(ThemeAndLogo.generate_rtl_stylesheet_button.replace('%S', 'on')));
      test('should click on "Save" button', () => client.waitForExistAndClick(ThemeAndLogo.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(Design.success_panel, 'Your RTL stylesheets has been generated successfully'));
    }, 'common_client');
  }
};