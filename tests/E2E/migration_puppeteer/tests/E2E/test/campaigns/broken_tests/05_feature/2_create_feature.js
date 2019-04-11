/**
 * This script is based on the scenario described in this test link
 * [id="PS-41"][Name="Create Feature"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const commonFeature = require('../../common_scenarios/feature');
const commonProduct = require('../../common_scenarios/product');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {Menu} = require('../../../selectors/BO/menu.js');
const {ProductList} = require('../../../selectors/BO/add_product_page');
const {FeatureSubMenu} = require('../../../selectors/BO/catalogpage/feature_submenu');
const {productPage} = require('../../../selectors/FO/product_page');
const welcomeScenarios = require('../../common_scenarios/welcome');

let promise = Promise.resolve();

let myFeatureData = {
  name: 'My Feature',
  values: ['Value 1', 'Value 2', 'Value 3']
};

let productData = {
  name: 'Mountain fox cushion',
  feature: [
    {
      name: 'My Feature',
      value: 'Value 1'
    }, {
      name: 'My feature',
      value: 'Value 1'
    }
  ]
};

/**
 * This script should be moved to the campaign full when this issue will be fixed
 * https://github.com/PrestaShop/PrestaShop/issues/11217
 **/
scenario('Create, edit, delete, delete with bulk actions "Feature" in the Back Office', () => {

  scenario('Test 1: Create "Feature"', () => {
    //create feature
    scenario('Login in the Back Office', client => {
      test('should open the browser', () => client.open());
      test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
    }, 'attribute_and_feature');
    welcomeScenarios.findAndCloseWelcomeModal();
    scenario('Create a new "Feature"', client => {
      test('should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
      test('should click on "Feature" subtab', () => client.waitForExistAndClick(Menu.Sell.Catalog.feature_tab));
      test('should click on "Add new feature" button', () => client.waitForExistAndClick(FeatureSubMenu.add_new_feature));
      test('should set the "Name" input', () => client.waitAndSetValue(FeatureSubMenu.name_input, myFeatureData.name + date_time));
      test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_button));
      test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
      test('should click on "Add new feature value" button', () => client.waitForExistAndClick(FeatureSubMenu.add_value_button, 3000));
      let dataValueNumber = myFeatureData.values.length;
      for (let i = 0; i < dataValueNumber; i++) {
        test('should select the created feature', () => client.waitAndSelectByVisibleText(FeatureSubMenu.feature_select, myFeatureData.name + date_time));
        test('should set the "Value" input', () => client.waitAndSetValue(FeatureSubMenu.value_input, myFeatureData.values[i]));
        if (i === dataValueNumber - 2 || i === dataValueNumber - 1) {
          test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_value_button));
          test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
          if (i === dataValueNumber - 2) {
            test('should search for the created feature', () => client.searchByValue(FeatureSubMenu.search_input.replace('%SEARCHBY', 'b!name'), FeatureSubMenu.search_button, myFeatureData.name + date_time));
            test('should click on "View" button', () => client.waitForExistAndClick(FeatureSubMenu.view_button));
            test('should click on "Add new feature value" button', () => client.waitForExistAndClick(FeatureSubMenu.add_value_button));
          }
        } else {
          test('should click on "Save then add another value" button', () => client.waitForExistAndClick(FeatureSubMenu.save_then_add_another_value_button));
        }
      }
    }, 'attribute_and_feature');
    commonFeature.checkFeatureInBO(myFeatureData);
    commonFeature.sortFeatures(FeatureSubMenu.feature_column.replace('%B', 2), 'ID', true);
    commonFeature.sortFeatures(FeatureSubMenu.feature_column.replace('%B', 3), 'Name');
    commonFeature.sortFeatures(FeatureSubMenu.feature_column.replace('%B', 5), 'Position', true);
    scenario('Check that the feature is well created in the Back Office', client => {
      test('should enter "3" in id search input then click on "search" button', () => client.searchByValue(FeatureSubMenu.search_input.replace('%SEARCHBY', 'id_feature'), FeatureSubMenu.search_button, 3));
      test('should get the number of features', () => client.getTextInVar(FeatureSubMenu.features_number_span, 'number_feature'));
      test('should verify that there is one result having id "3"', async () => {
        global.elementsTable = [];
        for (let j = 0; j < (parseInt(tab['number_feature'])); j++) {
          await client.getTableField(FeatureSubMenu.feature_column.replace('%B', 2), j);
        }
        await client.checkOneExistence('3');
      });
      test('should click on "Reset" button', () => client.waitForExistAndClick(FeatureSubMenu.reset_button));
      test('should enter "' + myFeatureData.name + date_time + '" in name field then click on "search" button', () => client.searchByValue(FeatureSubMenu.search_input.replace('%SEARCHBY', 'b!name'), FeatureSubMenu.search_button, myFeatureData.name + date_time));
      test('should verify that there is one result', () => {
        return promise
          .then(() => client.checkTextValue(FeatureSubMenu.features_number_span, '1'))
          .then(() => client.checkTextValue(FeatureSubMenu.feature_column.replace('%ID', 1).replace('%B', 3), myFeatureData.name + date_time));

      });
      test('should click on "Reset" button', () => client.waitForExistAndClick(FeatureSubMenu.reset_button));
      test('should enter "2" in position search input then click on "search" button', () => client.searchByValue(FeatureSubMenu.search_input.replace('%SEARCHBY', 'a!position'), FeatureSubMenu.search_button, 2));

      /**
       * This error is based on the bug described in this ticket
       * https://github.com/PrestaShop/PrestaShop/issues/11217
       **/
      test('should get the number of features', () => client.getTextInVar(FeatureSubMenu.features_number_span, 'number_feature'));
      test('should verify that there is one result having position "2"', async () => {
        global.elementsTable = [];
        for (let j = 0; j < (parseInt(tab['number_feature'])); j++) {
          await client.getTableField(FeatureSubMenu.feature_column.replace('%B', 5), j);
        }
        await client.checkOneExistence('2', 'position');
      });
      test('should click on "Reset" button', () => client.waitForExistAndClick(FeatureSubMenu.reset_button));
    }, 'attribute_and_feature');
    scenario('Edit a product in the Back Office then check product in the Front Office', client => {
      test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for "' + productData["name"] + '" product', () => client.searchByValue(AddProductPage.catalogue_filter_by_name_input, AddProductPage.catalogue_submit_filter_button, productData.name));
      test('should click on "Edit" button', () => client.waitForExistAndClick(ProductList.edit_button));
      for (let f = 0; f < productData['feature'].length; f++) {
        test('should click on "Add feature" button', () => {
          return promise
            .then(() => client.scrollWaitForExistAndClick(AddProductPage.add_feature_to_product_button));
        });
        test('should select the created feature', () => client.selectFeature(AddProductPage, productData['feature'][f].name + date_time, productData['feature'][f].value, f));
      }
      test('should close the symfony toolbar if exists', () => {
        return promise
          .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
      /**
       * This error is based on the bug described in this ticket
       * https://github.com/PrestaShop/PrestaShop/issues/10757
       **/
      test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.'));
      test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
      test('should switch to the Preview page in the Front Office', () => client.switchWindow(1));
      commonProduct.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name);
      test('should click on "Product details" tab', () => client.waitForExistAndClick(productPage.product_detail_tab));
      test('Verify that there is only "Value 1" for "' + productData['feature'][0].name + date_time + '"', () => client.isNotExisting(productPage.value_feature_text));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should click on "Add feature" button', () => client.scrollWaitForExistAndClick(AddProductPage.add_feature_to_product_button));
      test('should select the created feature and enter a customized value "custom 1"', () => client.selectFeatureCustomizedValue(AddProductPage, productData['feature'][0].name + date_time, 'custom 1', 1));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
      test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.'));
      test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
      test('should switch to the Preview page in the Front Office', () => client.switchWindow(2));
      commonProduct.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name);
      test('should click on "Product details" tab', () => client.waitForExistAndClick(productPage.product_detail_tab));
      test('should verify that "value 1" and "custom 1" exist', () => client.checkValuesFeature(productPage.product_value_text.replace('%B', 'first'), productData['feature'][0].value + '\n\n' + 'custom 1'));
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should click on "Add feature" button', () => client.scrollWaitForExistAndClick(AddProductPage.add_feature_to_product_button));
      test('should select the created feature and choose pre defined value "' + myFeatureData.values[1] + '"', () => client.selectFeature(AddProductPage, productData['feature'][0].name + date_time, myFeatureData.values[1], 2));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
      test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.'));
      test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
      test('should switch to the Preview page in the Front Office', () => client.switchWindow(3));
      commonProduct.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name);
      test('should click on "Product details" tab', () => client.waitForExistAndClick(productPage.product_detail_tab));
      test('should verify that "' + productData['feature'][0].name + date_time + '" exist', () => {
        return promise
          .then(() => client.pause(3000))
          .then(() => client.checkTextValue(productPage.product_feature_text.replace('%B', 'first'), productData['feature'][0].name + date_time));
      });
      test('should verify that "value 1", "value 2" and "custom 1" exist', () => {
        return promise
          .then(() => client.pause(2000))
          .then(() => client.checkValuesFeature(productPage.product_value_text.replace('%B', 'first'), productData['feature'][0].value + '\n' + 'Value 2' + '\n' + 'Custom 1'));
      });
      test('should go back to the Back Office', () => client.switchWindow(0));
      test('should click on "Add feature" button', () => client.scrollWaitForExistAndClick(AddProductPage.add_feature_to_product_button));
      test('should select "compositions" then choose pre defined value "cotton"', () => client.selectFeature(AddProductPage, 'compositions', 'Cotton', 3));
      test('should click on "Add feature" button', () => client.scrollWaitForExistAndClick(AddProductPage.add_feature_to_product_button));
      test('should select "compositions" then enter a customized value "compo custom"', () => client.selectFeatureCustomizedValue(AddProductPage, 'compositions', 'compo custom', 4));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
      test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.'));
      test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
      test('should switch to the Preview page in the Front Office', () => client.switchWindow(4));
      commonProduct.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name);
      test('should click on "Product details" tab', () => client.waitForExistAndClick(productPage.product_detail_tab));
      test('should verify that "' + productData['feature'][0].name + date_time + '" exist', () => {
        return promise
          .then(() => client.pause(3000))
          .then(() => client.checkTextValue(productPage.product_feature_text.replace('%B', 'last'), productData['feature'][0].name + date_time));
      });
      test('should verify that "value 1", "value 2" and "custom 1" exist', () => {
        return promise
          .then(() => client.pause(2000))
          .then(() => client.checkValuesFeature(productPage.product_value_text.replace('%B', 'last'), productData['feature'][0].value + '\n' + 'Value 2' + '\n' + 'Custom 1'));
      });
      test('should verify that "Compositions"', () => {
        return promise
          .then(() => client.pause(2000))
          .then(() => client.checkTextValue(productPage.product_feature_text.replace('%B', 'first'), 'Compositions'));
      });
      test('should verify that "Cotton" and "Compo Custom" exist', () => {
        return promise
          .then(() => client.pause(2000))
          .then(() => client.checkValuesFeature(productPage.product_value_text.replace('%B', 'first'), 'Cotton' + '\n' + 'Compo Custom'));
      });
      test('should go back to the product page', () => client.switchWindow(0));
      test('should change the product price tax included input', () => client.waitAndSetValue(AddProductPage.price_tax_included_input, 30));
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
      test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.'));
      test('should click on "Preview" button', () => client.waitForExistAndClick(AddProductPage.preview_buttons));
      test('should switch to the Preview page in the Front Office', () => client.switchWindow(5));
      commonProduct.clickOnPreviewLink(client, AddProductPage.preview_link, productPage.product_name);
      test('should click on "Product details" tab', () => client.waitForExistAndClick(productPage.product_detail_tab));
      test('should verify that "' + productData['feature'][0].name + date_time + '" exist', () => {
        return promise
          .then(() => client.pause(3000))
          .then(() => client.checkTextValue(productPage.product_feature_text.replace('%B', 'last'), productData['feature'][0].name + date_time));
      });
      test('should verify that "value 1", "value 2" and "custom 1" exist', () => {
        return promise
          .then(() => client.pause(2000))
          .then(() => client.checkValuesFeature(productPage.product_value_text.replace('%B', 'last'), productData['feature'][0].value + '\n' + 'Value 2' + '\n' + 'Custom 1'));
      });
      test('should verify that "Compositions"', () => {
        return promise
          .then(() => client.pause(2000))
          .then(() => client.checkTextValue(productPage.product_feature_text.replace('%B', 'first'), 'Compositions'));
      });
      test('should verify that "Cotton" and "Compo Custom" exist', () => {
        return promise
          .then(() => client.pause(2000))
          .then(() => client.checkValuesFeature(productPage.product_value_text.replace('%B', 'first'), 'Cotton' + '\n' + 'Compo Custom'));
      });
      test('should go back to the product page', () => client.switchWindow(0));
    }, 'product/product');
    scenario('Edit the product and delete all features', client => {
      test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should search for "' + productData["name"] + '" product', () => client.searchByValue(AddProductPage.catalogue_filter_by_name_input, AddProductPage.catalogue_submit_filter_button, productData.name));
      test('should click on "Edit" button', () => client.waitForExistAndClick(ProductList.edit_button));
      for (let j = 0; j < 5; j++) {
        test('should delete the feature number "' + parseInt(j + 1) + '"', async () => {
          await client.scrollTo(AddProductPage.customized_value_input.replace('%ID', j));
          await client.waitForExistAndClick(AddProductPage.delete_feature_button.replace('%ID', 1), 1000);
        });
        test('should click on "Yes" button', () => client.waitForExistAndClick(AddProductPage.alert_button.replace('%B', 'continue'), 1000));
      }
      test('should change the product price tax included input', () => {
        return promise
          .then(() => client.scrollTo(AddProductPage.price_tax_included_input))
          .then(() => client.waitAndSetValue(AddProductPage.price_tax_included_input, 22.68));
      });
      test('should close the symfony toolbar if exists', () => {
        return promise
          .then(() => client.waitForSymfonyToolbar(AddProductPage, 2000))
      });
      test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 5000));
      test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
      test('should click on "Reset" button', () => client.waitForExistAndClick(AddProductPage.catalog_reset_filter));
    }, 'product/product');

    commonFeature.deleteFeature(myFeatureData);

    scenario('Logout from the Back Office', client => {
      test('should logout successfully from the Back Office', () => client.signOutBO());
    }, 'common_client');
  }, 'attribute_and_feature', true);
}, 'common_client');
