const {CatalogPage} = require('../../selectors/BO/catalogpage/index');
const {AttributeSubMenu} = require('../../selectors/BO/catalogpage/attribute_submenu');
const {Menu} = require('../../selectors/BO/menu.js');
const {SearchProductPage} = require('../../selectors/FO/search_product_page');
let promise = Promise.resolve();

/**** Example of attribute data (all these properties are required) ****
 * let data = {
 *  name: 'attribute name',
 *  public_name: 'the public name tha will be displayed to customers',
 *  type: 'radio / select / color',
 *  values: {
 *    1: 'first value',
 *    2: 'second value',
 *    3: 'third value'
 *  }
 * };
 */

module.exports = {
  createAttribute(data) {
    scenario('Create a new "Attribute"', client => {
      test('Should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should click on "Add new attribute" button', () => client.waitForExistAndClick(AttributeSubMenu.add_new_attribute));
      test('should set the "Name" input', () => client.waitAndSetValue(AttributeSubMenu.name_input, data.name + date_time));
      test('should set the "Public name" input', () => client.waitAndSetValue(AttributeSubMenu.public_name_input, data.public_name + date_time));
      test('should choose the "Type" of attribute', () => client.waitAndSelectByValue(AttributeSubMenu.type_select, data.type));
      test('should click on "Save" button', () => client.waitForExistAndClick(AttributeSubMenu.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
      test('should search for the created attribute', () => client.searchByValue(AttributeSubMenu.search_input, AttributeSubMenu.search_button, data.name + date_time));
      test('should select the created attribute', () => client.waitForExistAndClick(AttributeSubMenu.selected_attribute));
      test('should add value to the created attribute', () => client.addValueToAttribute(AttributeSubMenu, data.values));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
    }, 'attribute_and_feature');
  },
  checkAttributeInFO(productName, data) {
    scenario('Check that the attribute is well created/updated in the Front Office', client => {
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productName + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should check the product attribute name', () => client.checkTextValue(SearchProductPage.attribute_name, data.name + date_time));
      test('should check the attribute values', () => client.checkTextValue(SearchProductPage.attribute_values, Object.keys(data.values).map((k) => data.values[k]), 'deepequal'));
    }, 'attribute_and_feature');
  },
  updateAttribute(data) {
    scenario('Update the created "Attribute"', client => {
      test('Should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should search for the created attribute', () => client.searchByValue(AttributeSubMenu.search_input, AttributeSubMenu.search_button, data.name + date_time));
      test('should click on "Edit" action', () => {
        return promise
          .then(() => client.clickOnAction(AttributeSubMenu.group_action_button, AttributeSubMenu.update_button))
          .then(() => client.editObjectData(data));
      });
      test('should set the "Name" input', () => client.waitAndSetValue(AttributeSubMenu.name_input, data.name + date_time));
      test('should set the "Public name" input', () => client.waitAndSetValue(AttributeSubMenu.public_name_input, data.public_name + date_time));
      test('should click on "Save" button', () => client.waitForExistAndClick(AttributeSubMenu.save_button));
      test('should click on "Reset" button', () => client.waitForExistAndClick(AttributeSubMenu.reset_button));
      test('should search for the created attribute', () => client.searchByValue(AttributeSubMenu.search_input, AttributeSubMenu.search_button, data.name + date_time));
      test('should select the attribute', () => client.waitForExistAndClick(AttributeSubMenu.selected_attribute));
      Object.keys(data.values).forEach(function (key) {
        test('should click on "Edit" action', () => client.waitForExistAndClick(AttributeSubMenu.update_value_button.replace('%POS', key)));
        test('should set the "Value" input', () => client.waitAndSetValue(AttributeSubMenu.value_input, data.values[key]));
        test('should click on "Save" button', () => client.waitForExistAndClick(AttributeSubMenu.save_value_button));
      });
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful update.'));
    }, 'attribute_and_feature');
  },
  deleteAttributeValue(data) {
    scenario('Delete the created "Attribute value"', client => {
      test('Should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should search for the created attribute', () => client.searchByValue(AttributeSubMenu.search_input, AttributeSubMenu.search_button, data.name + date_time));
      test('should select the attribute', () => client.waitForExistAndClick(AttributeSubMenu.selected_attribute));
      test('should delete the value of created attribute', () => {
        return promise
          .then(() => client.clickOnAction(AttributeSubMenu.value_action_group_button, AttributeSubMenu.delete_value_button, 'delete'))
          .then(() => client.deleteObjectElement(data.values, 1));
      });
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
    }, 'attribute_and_feature');
  },
  deleteAttribute(data) {
    scenario('Delete the created "Attribute"', client => {
      test('should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should search for the created attribute', () => client.searchByValue(AttributeSubMenu.search_input, AttributeSubMenu.search_button, data.name + date_time));
      test('should delete the created attribute', () => client.clickOnAction(AttributeSubMenu.group_action_button, AttributeSubMenu.delete_attribute_button, 'delete'));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
    }, 'attribute_and_feature');
  },
  checkDeletedAttributeInFO(productName) {
    scenario('Check that the attribute is well deleted in Front Office', client => {
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productName + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should check that the attribute has been deleted in the Front Office', () => client.checkDeleted(SearchProductPage.attribute_name));
    }, 'attribute_and_feature');
  },
  attributeBulkActions(data, action) {
    scenario(action.charAt(0).toUpperCase() + action.slice(1) + ' the created "Attribute" using the bulk actions', client => {
      test('should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should search for the created attribute', () => client.searchByValue(AttributeSubMenu.search_input, AttributeSubMenu.search_button, data.name + date_time));
      test('should select the created attribute', () => client.waitForExistAndClick(AttributeSubMenu.attribute_checkbox));
      test('should ' + action + ' the created attribute', () => client.clickOnAction(AttributeSubMenu.bulk_actions, AttributeSubMenu.delete_bulk_action, action));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nThe selection has been successfully', 'contain'));
    }, 'attribute_and_feature');
  }
};
