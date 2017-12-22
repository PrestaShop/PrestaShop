const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {AttributeSubMenu} = require('../../../selectors/BO/catalogpage/attribute_submenu');
const {FeatureSubMenu} = require('../../../selectors/BO/catalogpage/feature_submenu');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');

scenario('Create "Feature"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  scenario('Create a new "Feature"', client => {
    test('Should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(CatalogPage.menu_button, AttributeSubMenu.submenu));
    test('should click on "Feature" subtab', () => client.waitForExistAndClick(FeatureSubMenu.tabmenu));
    test('should click on "Add new feature" button', () => client.waitForExistAndClick(FeatureSubMenu.add_new_feature));
    test('should set the "Name" input', () => client.waitAndSetValue(FeatureSubMenu.name_input, 'Feature' + date_time));
    test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
    test('should search for the created feature', () => client.searchByValue(FeatureSubMenu.search_input, FeatureSubMenu.search_button, 'Feature' + date_time));
    test('should select the created feature', () => client.waitForExistAndClick(FeatureSubMenu.selected_feature));
    test('should click on "Add new feature value" button', () => client.waitForExistAndClick(FeatureSubMenu.add_value_button));
    test('should set the "Value" input', () => client.waitAndSetValue(FeatureSubMenu.value_input, 'feature value'));
    test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_value_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful creation.'));
  }, 'attribute_and_feature');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);

scenario('Create "Product"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  scenario('Create a new product', client => {
    test('should go to "Product Settings" page', () => client.waitForExistAndClick(AddProductPage.products_subtab));
    test('should click on "New Product" button', () => client.waitForExistAndClick(AddProductPage.new_product_button));
    test('should set the "Name" input', () => client.waitAndSetValue(AddProductPage.product_name_input, 'Feature'+' '+ date_time));
    test('should set the "Quantity" input', () => client.setQuantity(AddProductPage.quantity_shortcut_input));
    test('should set the "Price" input', () => client.setPrice(AddProductPage.priceTE_shortcut));
    test('should click on "Add feature" button', () => client.clickOnAddFeature(AddProductPage));
    test('should select the created feature', () => client.selectFeature(AddProductPage, 'Feature' + date_time, 'feature value'));
    test('should switch the product online', () => client.waitForExistAndClick(AddProductPage.product_online_toggle));
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(AddProductPage.validation_msg, 'Settings updated.'));
  }, 'create_product');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);

scenario('Check the feature creation', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'attribute_and_feature');
  scenario('Check that the feature is well created in Front Office', client => {
    test('should set the shop language to "English"', () => client.languageChange('english'));
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, 'Feature'));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should check that the name of created feature is : "'+ 'Feature' + date_time +'"', () => client.checkTextValue(SearchProductPage.feature_name, 'Feature' + date_time));
    test('should check that the value of created feature is : "Feature Value"', () => client.checkTextValue(SearchProductPage.feature_value, 'Feature Value'));
  }, 'attribute_and_feature');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);
