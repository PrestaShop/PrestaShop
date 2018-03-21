const {Localization} = require('../../selectors/BO/international/localization');
const {InternationalPage} = require('../../selectors/BO/international/index');
const {Menu} = require('../../selectors/BO/menu.js');

module.exports = {
  importLocalization(language) {
    scenario('Import a localization pack', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should click on "Localization pack you want to import" select', () => client.waitForExistAndClick(Localization.Localization.pack_select));
      test('should search for the localization from the select', () => client.waitAndSetValue(Localization.Localization.pack_search_input, language));
      test('should click on the first result from the select', () => client.waitForExistAndClick(Localization.Localization.pack_option));
      test('should click on "Import" button', () => client.waitForExistAndClick(Localization.Localization.import_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, "×\nLocalization pack imported successfully."));
    }, 'common_client');
  }
};