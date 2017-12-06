const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {CatalogPageBO} = require('../../../selectors/BO/catalogpage/index');
const {AttributeSubMenu} = require('../../../selectors/BO/catalogpage/attribute_submenu');
const {FeatureSubMenu} = require('../../../selectors/BO/catalogpage/feature_submenu');
const SearchProductPage = require('../../../selectors/FO/search_product_page');

scenario('Update "Feature"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  scenario('Update the created "Feature"', client => {
    test('Should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(CatalogPageBO.menu_button, AttributeSubMenu.submenu));
    test('should click on "Feature" subtab', () => client.waitForExistAndClick(FeatureSubMenu.tabmenu));
    test('should search for the created feature', () => client.searchByValue(FeatureSubMenu.search_input, FeatureSubMenu.search_button, 'Feature' + date_time));
    test('should click on "Edit" action', () => client.clickOnAction(FeatureSubMenu.select_option, FeatureSubMenu.update_feature_button));
    test('should set the "Name" input', () => client.waitAndSetValue(FeatureSubMenu.name_input, 'Feature' + date_time + 'update'));
    test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPageBO.success_panel, '×\nSuccessful update.'));
    test('should select the feature', () => client.waitForExistAndClick(FeatureSubMenu.selected_feature));
    test('should click on "Edit" action', () => client.waitForExistAndClick(FeatureSubMenu.update_feature_value_button));
    test('should set the "Value" input', () => client.waitAndSetValue(FeatureSubMenu.value_input, 'Feature value update'));
    test('should click on "Save" button', () => client.waitForExistAndClick(FeatureSubMenu.save_value_button));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPageBO.success_panel, '×\nSuccessful update.'));
  }, 'attribute_and_feature');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);

scenario('Check the feature modification', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'attribute_and_feature');
  scenario('Check that the feature is well updated in Front Office', client => {
    test('should set the shop language to "English"', () => client.languageChange('english'));
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should check that the name of created feature is : "'+ 'Feature' + date_time +'update"', () => client.checkTextValue(SearchProductPage.feature_name, 'Feature' + date_time + 'update'));
    test('should check that the value of created feature is : "Feature Value Update"', () => client.checkTextValue(SearchProductPage.feature_value, 'Feature Value Update'));
  }, 'attribute_and_feature');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);
