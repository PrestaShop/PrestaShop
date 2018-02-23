const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {AddProductPage} = require('../../../selectors/BO/add_product_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const {Products_list} = require('../../../selectors/BO/catalogpage/products');
const common_scenarios = require('../02_product/product');

let productData = [{
  name: 'DP',
  quantity: "50",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'a'
}, {
  name: 'DPBA',
  quantity: "5",
  price: '5',
  image_name: 'image_test.jpg',
  reference: 'b',
}];

scenario('Delete product', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'product/product');

  common_scenarios.createProduct(AddProductPage, productData[0]);

  scenario('Delete product "DP'+ date_time+'"' , client => {
    test('should go to "Product Settings" page', () => client.waitForExistAndClick(Menu.Sell.Catalog.catalog_menu));
    test('should set the product name "DP'+date_time+'" in the search input', () => client.waitAndSetValue(Products_list.name_search_input, productData[0].name+date_time));
    test('should click on the "Apply" button', () => client.waitForExistAndClick(Products_list.search_button));
    test('should click on the "dropdown" icon', () => client.waitForExistAndClick(Products_list.dropdown_toggle));
    test('should click on the "delete" icon', () => client.waitForExistAndClick(Products_list.delete_button));
    test('should click on the "delete now" button', () => client.waitForExistAndClick(Products_list.delete_confirmation));
    test('should verify the appearance of the green validation message', () => client.checkTextValue(Products_list.success_panel, 'Product successfully deleted.'));
  }, 'product/product');

}, 'product/product');