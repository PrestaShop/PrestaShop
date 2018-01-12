const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {CatalogPage} = require('../../../selectors/BO/catalogpage/index');
const {AttributeSubMenu} = require('../../../selectors/BO/catalogpage/attribute_submenu');
const {SearchProductPage} = require('../../../selectors/FO/search_product_page');

scenario('Delete "Attribute"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'attribute_and_feature');
  scenario('Delete the created "Attribute"', client => {
    test('Should go to "Attributes & Features" page', () => client.goToSubtabMenuPage(CatalogPage.menu_button, AttributeSubMenu.submenu));
    test('should search for the created attribute', () => client.searchByValue(AttributeSubMenu.search_input, AttributeSubMenu.search_button, 'attribute' + date_time));
    test('should delete the created attribute', () => client.clickOnAction(AttributeSubMenu.group_action_button, AttributeSubMenu.delete_attribute_button, 'delete'));
    test('should verify the appearance of the green validation', () => client.checkTextValue(CatalogPage.success_panel, '×\nSuccessful deletion.'));
  }, 'attribute_and_feature');
  scenario('Logout from the Back Office', client => {
    test('should logout successfully from the Back Office', () => client.signOutBO());
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);

scenario('Check the attribute deletion', () => {
  scenario('Login in the Front Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Front Office', () => client.signInFO(AccessPageFO));
  }, 'attribute_and_feature');
  scenario('Check that the attribute is well deleted in Front Office', client => {
    test('should set the shop language to "English"', () => client.changeLanguage('english'));
    test('should search for the product', () => client.searchByValue(SearchProductPage.search_input, SearchProductPage.search_button, 'Att' + date_time));
    test('should go to the product page', () => client.waitForExistAndClick(SearchProductPage.product_result_name));
    test('should Check that the attribute has been deleted in the Front Office', () => client.checkDeleted(SearchProductPage.attribute_name));
  }, 'attribute_and_feature');
  scenario('Logout from the Front Office', client => {
    test('should logout successfully from the Front Office', () => client.signOutFO(AccessPageFO));
  }, 'attribute_and_feature');
}, 'attribute_and_feature', true);
