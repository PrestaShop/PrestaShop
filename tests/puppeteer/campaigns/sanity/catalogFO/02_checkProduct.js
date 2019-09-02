// Using chai
const {expect} = require('chai');

// Importing pages
const HomePage = require('../../../pages/FO/home');
const ProductPage = require('../../../pages/FO/product');
const ProductData = require('../../data/FO/product');

let page;
let homePage;
let productPage;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  homePage = await (new HomePage(page));
  productPage = await (new ProductPage(page));
};

/*
  Open the FO home page
  Check the first product page
 */
global.scenario('Check the Product page', () => {
  test('should open the shop page', async () => {
    await homePage.goTo(global.URL_FO);
    await homePage.checkHomePage();
  });
  test('should go to the first product page', () => homePage.goToProductPage('1'));
  test('should check the product page', () => productPage.checkProduct(ProductData.firstProductData));
}, init, true);
