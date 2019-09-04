// Using chai
const {expect} = require('chai');

// Importing pages
const HomePage = require('../../../pages/FO/home');
const ProductPage = require('../../../pages/FO/product');
const CartPage = require('../../../pages/FO/cart');
const CartData = require('../../data/FO/cart');

let page;
let homePage;
let productPage;
let cartPage;
let totalTTC = 0;
let itemsNumber = 0;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
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
global.scenario('Check the Product page', () => {
  test('should open the shop page', async () => {
    await homePage.goTo(global.URL_FO);
    await homePage.checkHomePage();
  });
  test('should go to the first product page', () => homePage.goToProductPage('1'));
  test('should add product to the cart', () => productPage.addProductToTheCart());
  test('should check that there is one notification', async () => {
    const notificationNumber = await homePage.getNumberFromText(homePage.cartProductsCount);
    await expect(notificationNumber).to.be.equal(1);
  });
  test('should go to the home page', () => homePage.goToHomePage());
  test('should go to the second product page', () => homePage.goToProductPage('2'));
  test('should add product to the cart', () => productPage.addProductToTheCart());
  test('should check that there are two notifications', async () => {
    const notificationNumber = await homePage.getNumberFromText(homePage.cartProductsCount);
    await expect(notificationNumber).to.be.equal(2);
  });
  test('should check the first product details', async () => {
    await cartPage.checkProductInCart(CartData.customCartData.firstProduct, '1');
  });
  test('should check the second product details', async () => {
    await cartPage.checkProductInCart(CartData.customCartData.secondProduct, '2');
  });
  test('should get the Total TTC', async () => {
    totalTTC = await cartPage.getNumberFromText(cartPage.cartTotalTTC);
  });
  test('should get the product number', async () => {
    itemsNumber = await cartPage.getNumberFromText(cartPage.itemsNumber);
  });
  test('should edit the quantity of the first product ordered', async () => {
    await cartPage.editProductQuantity('1', '3');
  });
  test('should edit the quantity of the second product ordered', async () => {
    await cartPage.editProductQuantity('2', '2');
  });
  test('should check that the Total order price is changed', async () => {
    const numberOfProducts = await cartPage.getNumberFromText(cartPage.cartTotalTTC);
    await expect(numberOfProducts).to.be.above(totalTTC);
  });
  test('should check that the Items number is changed', async () => {
    const numberOfItems = await cartPage.getNumberFromText(cartPage.itemsNumber);
    await expect(numberOfItems).to.be.above(itemsNumber);
  });
}, init, true);
