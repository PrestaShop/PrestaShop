const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {FeatureSubMenu} = require('../../selectors/BO/catalogpage/feature_submenu');
const {Menu} = require('../../selectors/BO/menu.js');
const {SearchProductPage} = require('../../selectors/FO/search_product_page');
let promise = Promise.resolve();

/**** Example of feature data (all these properties are required) ****
 * let featureData = {
 *  name: 'Feature name',
 *  values: {
 *    1: 'Feature Value'
 *  }
 * };
 *
 */
module.exports = {
  createFeature(data) {
    scenario('Create a new "Feature"', client => {
      test('should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should click on "Feature" subtab', () => client.waitForExistAndClick(Menu.Sell.Catalog.feature_tab));
      test('should click on "Add new feature" button', () => client.waitForExistAndClick(FeatureSubMenu.add_new_feature));
      test('should set the "Name" input', () => client.waitAndSetValue(FeatureSubMenu.name_input, data.name + date_time));
      test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
      test('should search for the created feature', () => client.searchByValue(FeatureSubMenu.search_input.replace('%SEARCHBY', 'name'), FeatureSubMenu.search_button, data.name + date_time));
      test('should select the created feature', () => client.waitForExistAndClick(FeatureSubMenu.selected_feature));
      test('should click on "Add new feature value" button', () => client.waitForExistAndClick(FeatureSubMenu.add_value_button));
      test('should set the "Value" input', () => client.waitAndSetValue(FeatureSubMenu.value_input, data.values[1]));
      test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_value_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
    }, 'attribute_and_feature');
  },
  checkFeatureInFO(productName, data) {
    scenario('Check that the feature is well created/updated in the Front Office', client => {
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productName + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should check the feature name', () => client.checkTextValue(SearchProductPage.feature_name, data.name + date_time));
      test('should check the feature value', () => client.checkTextValue(SearchProductPage.feature_value, data.values[1]));
    }, 'attribute_and_feature');
  },
  updateFeature(data) {
    scenario('Update the created "Feature"', client => {
      test('should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should click on "Feature" subtab', () => client.waitForExistAndClick(Menu.Sell.Catalog.feature_tab));
      test('should search for the created feature', () => client.searchByValue(FeatureSubMenu.search_input.replace('%SEARCHBY', 'name'), FeatureSubMenu.search_button, data.name + date_time));
      test('should click on "Edit" action', () => {
        return promise
          .then(() => client.clickOnAction(FeatureSubMenu.select_option, FeatureSubMenu.update_feature_button))
          .then(() => client.editObjectData(data));
      });
      test('should set the "Name" input', () => client.waitAndSetValue(FeatureSubMenu.name_input, data.name + date_time));
      test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful update.'));
      test('should click on "Reset" button', () => client.waitForExistAndClick(FeatureSubMenu.reset_button));
      test('should search for the updated feature', () => client.searchByValue(FeatureSubMenu.search_input.replace('%SEARCHBY', 'name'), FeatureSubMenu.search_button, data.name + date_time));
      test('should select the feature', () => client.waitForExistAndClick(FeatureSubMenu.selected_feature));
      test('should click on "Edit" action', () => client.waitForExistAndClick(FeatureSubMenu.update_feature_value_button));
      test('should set the "Value" input', () => client.waitAndSetValue(FeatureSubMenu.value_input, data.values[1]));
      test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_value_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful update.'));
    }, 'attribute_and_feature');
  },
  deleteFeature(data) {
    scenario('Delete the created "Feature"', client => {
      test('should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should click on "Feature" subtab', () => client.waitForExistAndClick(Menu.Sell.Catalog.feature_tab));
      test('should search for the created feature', () => client.searchByValue(FeatureSubMenu.search_input.replace('%SEARCHBY', 'name'), FeatureSubMenu.search_button, data.name + date_time));
      test('should delete the created feature', () => client.clickOnAction(FeatureSubMenu.select_option, FeatureSubMenu.delete_feature, 'delete'));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
    }, 'attribute_and_feature');
  },
  checkDeletedFeatureInFO(productName) {
    scenario('Check that the feature does not exist in the Front Office', client => {
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productName + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should check that the feature has been deleted in the Front Office', () => client.checkDeleted(SearchProductPage.feature_name));
    }, 'attribute_and_feature');
  },
  featureBulkActions(data, action) {
    scenario(action.charAt(0).toUpperCase() + action.slice(1) + ' the created "Feature" using the bulk actions', client => {
      test('should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should click on "Feature" subtab', () => client.waitForExistAndClick(Menu.Sell.Catalog.feature_tab));
      test('should search for the created feature', () => client.searchByValue(FeatureSubMenu.search_input.replace('%SEARCHBY', 'name'), FeatureSubMenu.search_button, data.name + date_time));
      test('should click on checkbox option', () => client.waitForExistAndClick(FeatureSubMenu.feature_checkbox));
      test('should ' + action + ' the created feature', () => client.clickOnAction(FeatureSubMenu.feature_bulk_actions, FeatureSubMenu.feature_delete_bulk_action, 'delete'));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nThe selection has been successfully deleted.'));
    }, 'attribute_and_feature');
  }
};
