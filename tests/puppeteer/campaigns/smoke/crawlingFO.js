// Importing pages
const HomePage = require('../../pages/FO/home');
const ProductPage = require('../../pages/FO/product');
const CartPage = require('../../pages/FO/cart');
const ProductData = require('../data/FO/product');
const CartData = require('../data/FO/cart');

let page;
let homePage;
let productPage;
let cartPage;
// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  homePage = await (new HomePage(page));
  productPage = await (new ProductPage(page));
  cartPage = await (new CartPage(page));
};

/*
  Open the FO home page
  Crawl and check a few key pages
 */
global.scenario('Check the Front Office', () => {
  test('should open the shop page', async () => {
    await homePage.goTo(global.URL_FO);
    await homePage.checkHomePage();
  });
  test('should go to the first product page', () => homePage.goToProductPage('1'));
  test('should check the product page', () => productPage.checkProduct(ProductData.firstProductData));
  test('should add product to the cart', () => productPage.addProductToTheCart());
  test('should check the cart details', () => cartPage.checkCartDetails(CartData.customCartData, '1'));
  test('should checkout a product', () => cartPage.clickOnProceedToCheckout());
}, init, true);
