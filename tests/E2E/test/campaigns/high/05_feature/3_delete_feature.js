const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {FeatureSubMenu} = require('../../../selectors/BO/catalogpage/feature_submenu');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');

scenario('Delete "Feature"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  scenario('Delete the created "Feature"', client => {
    test('Should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.attributes_features_submenu));
    test('should click on "Feature" subtab', () => client.waitForExistAndClick(FeatureSubMenu.tabmenu));
    test('should search for the created feature', () => client.searchByValue(FeatureSubMenu.search_input, FeatureSubMenu.search_button, 'Feature' + date_time));
    test('should delete the created feature', () => client.clickOnAction(FeatureSubMenu.select_option, FeatureSubMenu.delete_feature, 'delete'));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
  }, 'attribute_and_feature');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);

scenario('Check the feature deletion', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'attribute_and_feature');
  scenario('Check that the feature does not exist in the Front Office', client => {
    test('should set the shop language to "English"', () => client.changeLanguage('english'));
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, "Feat" + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should check that the feature has been deleted in the Front Office', () => client.checkDeleted(SearchProductPage.feature_name));
  }, 'attribute_and_feature');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);
