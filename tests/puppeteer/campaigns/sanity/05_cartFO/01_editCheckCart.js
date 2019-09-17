const {expect} = require('chai');
const helper = require('../../utils/helpers');
// Using chai

// Importing pages
const HomePage = require('../../../pages/FO/home');
const ProductPage = require('../../../pages/FO/product');
const CartPage = require('../../../pages/FO/cart');
const CartData = require('../../data/FO/cart');

let browser;
let page;
let homePage;
let productPage;
let cartPage;
let totalTTC = 0;
let itemsNumber = 0;

// creating pages objects in a function
const init = async function () {
  homePage = await (new HomePage(page));
  productPage = await (new ProductPage(page));
  cartPage = await (new CartPage(page));
};

/*
  Open the FO home page
  Add the first product to the cart
  Add the second product to the cart
  Check the cart
  Edit the cart and check it
 */
describe('Check the Product page', async () => {
  // before and after functions
  before(async () => {
    browser = await helper.createBrowser();
    page = await browser.newPage();
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'en-GB',
    });
    await init();
  });
  after(async () => {
    await browser.close();
  });
  // Steps
  it('should open the shop page', async () => {
    await homePage.goTo(global.URL_FO);
    await homePage.checkHomePage();
  });
  it('should go to the first product page', async () => {
    await homePage.goToProductPage('1');
  });
  it('should add product to the cart', async () => {
    await productPage.addProductToTheCart();
  });
  it('should check that the number of products in Cart was updated in header', async () => {
    // getNumberFromText is used to get the notifications number in the cart
    const notificationsNumber = await homePage.getNumberFromText(homePage.cartProductsCount);
    await expect(notificationsNumber).to.be.equal(1);
  });
  it('should go to the home page', async () => {
    await homePage.goToHomePage();
  });
  it('should go to the second product page', async () => {
    await homePage.goToProductPage('2');
  });
  it('should add product to the cart', async () => {
    await productPage.addProductToTheCart();
  });
  it('should check that the number of products in Cart was updated in header', async () => {
    // getNumberFromText is used to get the notifications number in the cart
    const notificationsNumber = await homePage.getNumberFromText(homePage.cartProductsCount);
    await expect(notificationsNumber).to.be.equal(2);
  });
  it('should check the first product details', async () => {
    await cartPage.checkProductInCart(CartData.customCartData.firstProduct, '1');
  });
  it('should check the second product details', async () => {
    await cartPage.checkProductInCart(CartData.customCartData.secondProduct, '2');
  });
  it('should get the Total TTC', async () => {
    // getNumberFromText is used to get the Total TTC price
    totalTTC = await cartPage.getNumberFromText(cartPage.cartTotalTTC);
  });
  it('should get the product number', async () => {
    // getNumberFromText is used to get the products number
    itemsNumber = await cartPage.getNumberFromText(cartPage.itemsNumber);
  });
  it('should edit the quantity of the first product ordered', async () => {
    await cartPage.editProductQuantity('1', '3');
  });
  it('should edit the quantity of the second product ordered', async () => {
    await cartPage.editProductQuantity('2', '2');
  });
  it('should check that the Total order price is changed', async () => {
    // getNumberFromText is used to get the new Total TTC price
    const totalPrice = await cartPage.getNumberFromText(cartPage.cartTotalTTC, 2000);
    await expect(totalPrice).to.be.above(totalTTC);
  });
  it('should check that the Items number is changed', async () => {
    // getNumberFromText is used to get the new products number
    const productsNumber = await cartPage.getNumberFromText(cartPage.itemsNumber);
    await expect(productsNumber).to.be.above(itemsNumber);
  });
});
