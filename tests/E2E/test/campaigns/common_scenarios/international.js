const {Localization} = require('../../selectors/BO/international/localization');
const {InternationalPage} = require('../../selectors/BO/international/index');
const {Menu} = require('../../selectors/BO/menu.js');

module.exports = {
  importLocalization(language) {
    scenario('Import a localization pack', client => {
      test('should go to "Localization" page', () => client.goToSubtabMenuPage(Menu.Improve.International.international_menu, Menu.Improve.International.localization_submenu));
      test('should search for the localization from the select', () => client.selectByVisibleText(Localization.Localization.pack_select, language));
      test('should click on "Import" button', () => client.waitForExistAndClick(Localization.Localization.import_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(InternationalPage.success_panel, "Localization pack imported successfully."));
    }, 'common_client');
  }
};
