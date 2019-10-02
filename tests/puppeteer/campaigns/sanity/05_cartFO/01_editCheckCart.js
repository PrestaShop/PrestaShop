// Using chai
const {expect} = require('chai');
const helper = require('../../utils/helpers');

// Importing pages
const HomePage = require('../../../pages/FO/home');
const ProductPage = require('../../../pages/FO/product');
const CartPage = require('../../../pages/FO/cart');
const CartData = require('../../data/FO/cart');

let browser;
let page;
let totalTTC = 0;
let itemsNumber = 0;

// creating pages objects in a function
const init = async function () {
  return {
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    cartPage: new CartPage(page),
  };
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
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    await page.setExtraHTTPHeaders({
      'Accept-Language': 'en-GB',
    });
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  it('should open the shop page', async function () {
    await this.pageObjects.homePage.goTo(global.FO.URL);
    await this.pageObjects.homePage.checkHomePage();
  });
  it('should go to the first product page', async function () {
    await this.pageObjects.homePage.goToProductPage('1');
  });
  it('should add product to the cart', async function () {
    await this.pageObjects.productPage.addProductToTheCart();
  });
  it('should check that the number of products in Cart was updated in header', async function () {
    // getNumberFromText is used to get the notifications number in the cart
    const notificationsNumber = await this.pageObjects.homePage
      .getNumberFromText(this.pageObjects.homePage.cartProductsCount);
    await expect(notificationsNumber).to.be.equal(1);
  });
  it('should go to the home page', async function () {
    await this.pageObjects.homePage.goToHomePage();
    await this.pageObjects.homePage.checkHomePage();
  });
  it('should go to the second product page', async function () {
    await this.pageObjects.homePage.goToProductPage('2');
  });
  it('should add product to the cart', async function () {
    await this.pageObjects.productPage.addProductToTheCart();
  });
  it('should check that the number of products in Cart was updated in header', async function () {
    // getNumberFromText is used to get the notifications number in the cart
    const notificationsNumber = await this.pageObjects.homePage
      .getNumberFromText(this.pageObjects.homePage.cartProductsCount);
    await expect(notificationsNumber).to.be.equal(2);
  });
  it('should check the first product details', async function () {
    const result = await this.pageObjects.cartPage.checkProductInCart(CartData.customCartData.firstProduct, '1');
    await expect(result.name).to.be.true;
    await expect(result.price).to.be.true;
    await expect(result.quantity).to.be.true;
  });
  it('should check the second product details', async function () {
    const result = await this.pageObjects.cartPage.checkProductInCart(CartData.customCartData.secondProduct, '2');
    await expect(result.name).to.be.true;
    await expect(result.price).to.be.true;
    await expect(result.quantity).to.be.true;
  });
  it('should get the Total TTC', async function () {
    // getNumberFromText is used to get the Total TTC price
    totalTTC = await this.pageObjects.cartPage.getNumberFromText(this.pageObjects.cartPage.cartTotalTTC);
  });
  it('should get the product number', async function () {
    // getNumberFromText is used to get the products number
    itemsNumber = await this.pageObjects.cartPage.getNumberFromText(this.pageObjects.cartPage.itemsNumber);
  });
  it('should edit the quantity of the first product ordered', async function () {
    await this.pageObjects.cartPage.editProductQuantity('1', '3');
  });
  it('should edit the quantity of the second product ordered', async function () {
    await this.pageObjects.cartPage.editProductQuantity('2', '2');
  });
  it('should check that the Total order price is changed', async function () {
    // getNumberFromText is used to get the new Total TTC price
    const totalPrice = await this.pageObjects.cartPage.getNumberFromText(this.pageObjects.cartPage.cartTotalTTC, 2000);
    await expect(totalPrice).to.be.above(totalTTC);
  });
  it('should check that the Items number is changed', async function () {
    // getNumberFromText is used to get the new products number
    const productsNumber = await this.pageObjects.cartPage.getNumberFromText(this.pageObjects.cartPage.itemsNumber);
    await expect(productsNumber).to.be.above(itemsNumber);
  });
});
