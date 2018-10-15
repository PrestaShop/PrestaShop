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
      test('should select the created attribute', () => {
        return promise
          .then(() => client.getTextInVar(AttributeSubMenu.attribute_id, data.name + "_id"))
          .then(() => client.waitForExistAndClick(AttributeSubMenu.attribute_name));
      });
      test('should click on "Add new value" button', () => client.waitForExistAndClick(AttributeSubMenu.add_value_button));
      Object.keys(data.values).forEach(function (key) {
        test('should set the "Value" input', () => client.waitAndSetValue(AttributeSubMenu.value_input, data.values[key].value));
        if (data.values[key].hasOwnProperty('color')) {
          test('should set the "Color" input', () => client.waitAndSetValue(AttributeSubMenu.color_input, data.values[key].color));
        }
        if (data.values[key].hasOwnProperty('file')) {
          test('should upload the texture file', () => client.uploadPicture(data.values[key].file, AttributeSubMenu.texture_input_file));
        }
        if(Object.keys(data.values).length > 1) {
          test('should click on "Save" button', () => client.waitForExistAndClick(AttributeSubMenu.save_and_add_button));
        } else {
          test('should click on "Save" button', () => client.waitForExistAndClick(AttributeSubMenu.save_value_button));
        }
      });
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
    }, 'attribute_and_feature');
  },
  checkAttributeInFO(productName, data) {
    scenario('Check that the attribute is well created/updated in the Front Office', client => {
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, productName + date_time));
      test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
      test('should check the product attribute name', () => client.checkTextValue(SearchProductPage.attribute_name, data.name + date_time));
      test('should check the attribute values', () => client.checkTextValue(SearchProductPage.attribute_radio_values, Object.keys(data.values).map((k) => data.values[k].value), 'deepequal'));
    }, 'attribute_and_feature');
  },
  checkAllAttributeTypeInFO(AccessPageBO, productPage, productName, data) {
    scenario('Check that the attribute is well created/updated in the Front Office', client => {
      test('should go to the Front Office', () => {
        return promise
          .then(() => client.waitForExistAndClick(AccessPageBO.shopname, 3000))
          .then(() => client.switchWindow(1))
      });
      test('should set the shop language to "English"', () => client.changeLanguage());
      test('should click on "SEE ALL PRODUCTS" link', () => client.scrollWaitForExistAndClick(productPage.see_all_products));
      for (let i = 0; i <= pagination; i++) {
        test('should check the ' + productName + ' product existence in the ' + (Number(i) + 1) + ' page', () => {
          return promise
            .then(() => client.pause(4000))
            .then(() => client.isVisible(productPage.productLink.replace('%PRODUCTNAME', productName + date_time)))
            .then(() => client.middleClickWhenVisible(productPage.productLink.replace('%PRODUCTNAME', productName + date_time)));
        });
        if (i !== pagination) {
          test('should click on "NEXT" button', () => {
            return promise
              .then(() => client.isVisible(productPage.pagination_next))
              .then(() => {
                if (global.isVisible) {
                  client.clickNextOrPrevious(productPage.pagination_next);
                }
              });
          });
        }
      }
      test('should go to the product page', () => client.switchWindow(2));
      test('should check all attributes name of the product', () => client.checkTextValue(SearchProductPage.attribute_name, Object.keys(data).map((k) => data[k].name + date_time), 'deepequal'));
      Object.keys(data).forEach(function (key) {
        if(data[key].type === 'select') {
          test('should check the attribute select values', () => client.checkTextValue(SearchProductPage.attribute_select_values.replace('%ID', global.tab[data[key].name + "_id"]), Object.keys(data[key].values).map((k) => data[key].values[k].value), 'deepequal'));
        } else if(data[key].type === 'radio') {
          test('should check the attribute radio values', () => client.checkTextValue(SearchProductPage.attribute_radio_values, Object.keys(data[key].values).map((k) => data[key].values[k].value), 'deepequal'));
        } else if (data[key].type === 'color') {
          test('should check the attribute color values', () => client.checkTextValue(SearchProductPage.attribute_color_and_texture_values, Object.keys(data[key].values).map((k) => data[key].values[k].value), 'deepequal'));
        }
      });
      test('should go back to the Back Office', () => client.switchWindow(0));
    }, 'product/product');
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
      test('should select the attribute', () => client.waitForExistAndClick(AttributeSubMenu.attribute_name));
      Object.keys(data.values).forEach(function (key) {
        test('should click on "Edit" action', () => client.waitForExistAndClick(AttributeSubMenu.update_value_button.replace('%POS', key)));
        test('should set the "Value" input', () => client.waitAndSetValue(AttributeSubMenu.value_input, data.values[key].value));
        test('should click on "Save" button', () => client.waitForExistAndClick(AttributeSubMenu.save_value_button));
      });
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful update.'));
    }, 'attribute_and_feature');
  },
  deleteAttributeValue(data) {
    scenario('Delete the created "Attribute value"', client => {
      test('Should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should search for the created attribute', () => client.searchByValue(AttributeSubMenu.search_input, AttributeSubMenu.search_button, data.name + date_time));
      test('should select the attribute', () => client.waitForExistAndClick(AttributeSubMenu.attribute_name));
      test('should delete the value of created attribute', () => {
        return promise
          .then(() => client.clickOnAction(AttributeSubMenu.value_action_group_button, AttributeSubMenu.delete_value_button, 'delete'))
          .then(() => client.deleteObjectElement(data.values, 1));
      });
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
    }, 'attribute_and_feature');
  },
  deleteAttribute(data, close = false) {
    scenario('Delete the created "Attribute"', client => {
      test('should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should search for the created attribute', () => client.searchByValue(AttributeSubMenu.search_input, AttributeSubMenu.search_button, data.name + date_time));
      test('should delete the created attribute', () => client.clickOnAction(AttributeSubMenu.group_action_button, AttributeSubMenu.delete_attribute_button, 'delete'));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
    }, 'attribute_and_feature', close);
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
      test('should ' + action + ' the created attribute', () => client.clickOnAction(AttributeSubMenu.bulk_actions_button, AttributeSubMenu.delete_bulk_action_button, action));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nThe selection has been successfully', 'contain'));
    }, 'attribute_and_feature');
  }
};
