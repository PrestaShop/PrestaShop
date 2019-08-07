// Importing pages
const PAGE = require('../../pages/FO/home');
const PRODUCT_PAGE = require('../../pages/FO/product');
const CART_PAGE = require('../../pages/FO/cart');
const productData = require('../data/FO/product');
const cartData = require('../data/FO/cart');

let page;
let HOME;
let PRODUCT;
let CART;

// Init objects needed
const init = async () => {
  page = await global.browser.newPage();
  HOME = await (new PAGE(page));
  PRODUCT = await (new PRODUCT_PAGE(page));
  CART = await (new CART_PAGE(page));
};

// Scenario
global.scenario('Check the Front Office', () => {
  test('should open the shop page', async () => {
    await HOME.goTo(global.URL_FO);
    await HOME.checkHomePage();
  });
  test('should go to the first product page', () => HOME.goToProductPage('1'));
  test('should check the product page', () => PRODUCT.checkProduct(productData.firstProductData));
  test('should add product to the cart', () => PRODUCT.addProductToTheCart());
  test('should check the cart details', () => CART.checkCartDetails(cartData.customCartData, '1'));
  test('should checkout a product', () => CART.clickOnProceedToCheckout());
}, init, true);
