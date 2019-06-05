/**
 * This script is based on the scenario described in this test link
 * [id="PS-45"][Name="New brand"]
 **/

const {AccessPageBO} = require('../../../selectors/BO/access_page');
const {Menu} = require('../../../selectors/BO/menu.js');
const welcomeScenarios = require('../../common_scenarios/welcome');
const {AccessPageFO} = require('../../../selectors/FO/access_page');
const {SiteMapPageFO} = require('../../../selectors/FO/site_map_page');
const commonProducts = require('../../common_scenarios/product');
const commonManufacturers = require('../../common_scenarios/manufacturers');
const {ProductList, AddProductPage} = require('../../../selectors/BO/add_product_page');
let promise = Promise.resolve();
let productData = {
  name: 'TEST PRODUCT BRAND',
  quantity: '10',
  price: '12',
  image_name: '1.png',
  reference: 'pBrand'
};
let brandData = {
  name: 'PrestaShop',
  shortDescription: 'short description',
  description: 'description',
  picture: 'prestashop.png',
  metaTitle: 'meta title',
  metaDescription: 'meta description',
  metaKeywords: {
    1: 'first key',
    2: 'second key'
  },
};

scenario('Create "Brand"', () => {
  scenario('Login in the Back Office', client => {
    test('should open the browser', () => client.open());
    test('should login successfully in the Back Office', () => client.signInBO(AccessPageBO));
  }, 'common_client');
  welcomeScenarios.findAndCloseWelcomeModal();
  commonProducts.createProduct(AddProductPage, productData);
  commonManufacturers.createBrand(brandData);
  scenario('Check brand in Front Office', client => {
    test('should click on "Shop name" then go to the Front Office', () => {
      return promise
        .then(() => client.waitForExistAndClick(AccessPageBO.shopname))
        .then(() => client.switchWindow(1))
        .then(() => client.changeLanguage());
    });
    test('should click on "Sitemap" link on the footer', () => client.scrollWaitForExistAndClick(AccessPageFO.sitemap));
    test('should click on "Brands" link', () => client.waitForExistAndClick(SiteMapPageFO.brands_link));
    test('should check that the "Image" of the created brand is displayed', () => client.isExisting(SiteMapPageFO.brands_image.replace('%BRAND', 'PrestaShop' + date_time)));
    test('should check that the "Short description" of the created brand is displayed', () => client.checkTextValue(SiteMapPageFO.last_brand_description_text.replace('%NAME', 'PrestaShop' + date_time) + '/../..', 'short description','contain'));
    test('should click on "PrestaShop' + date_time + '" link', () => client.waitForExistAndClick(SiteMapPageFO.name_brand_link.replace('%NAME', 'PrestaShop' + date_time)));
    test('should check the "Short description" text of the created brand', () => client.checkTextValue(SiteMapPageFO.manufacturer_short_description_text, 'short description'));
    test('should check the "Description" text of the created brand', () => client.checkTextValue(SiteMapPageFO.manufacturer_description_text, 'description'));
  }, 'common_client');

  scenario('Edit the created product', client => {
    test('should go back to the Back Office', () => client.switchWindow(0));
    test('should go to "Catalog > Products" page', () => client.goToSubtabMenuPage(Menu.Sell.Catalog.catalog_menu, Menu.Sell.Catalog.products_submenu));
    test('should search for product by name', () => client.searchProductByName(productData.name + date_time));
    test('should click on "Edit" button', () => client.waitForExistAndClick(ProductList.edit_button));
    test('should click on "Add a brand" button', () => client.scrollWaitForExistAndClick(AddProductPage.product_add_brand_btn, 50));
    test('should select the created brand', () => {
      return promise
        .then(() => client.waitForExistAndClick(AddProductPage.product_brand_select))
        .then(() => client.waitForExistAndClick(AddProductPage.brand_option.replace('%BRAND', 'PrestaShop' + date_time)));
    });
    test('should click on "Save" button', () => client.waitForExistAndClick(AddProductPage.save_product_button, 7000));
  }, 'product/check_product');

  scenario('Check brand in Front Office', client => {
    test('should go to the Front Office', () => client.switchWindow(1));
    test('should click on "Sitemap" link on the footer', () => client.scrollWaitForExistAndClick(AccessPageFO.sitemap));
    test('should click on "Brands" link to display all brands', () => client.waitForExistAndClick(SiteMapPageFO.brands_link));
    test('should click on "1 product" link related to the created brand', () => client.waitForExistAndClick(SiteMapPageFO.brand_product_link.replace('%NAME', 'PrestaShop' + date_time)));
    test('should check the existence of the created product', () => client.waitForVisible(SiteMapPageFO.list_product_link.replace('%TEXT', date_time)));
    test('should go to the Back Office', () => client.switchWindow(0));
  }, 'common_client');
  commonProducts.deleteProduct(AddProductPage, productData);

  scenario('Logout from the Back Office', client => {
    test('should logout successfully from Back Office', () => client.signOutBO());
  }, 'common_client');

}, 'common_client', true);
