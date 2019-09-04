// Using chai
const {expect} = require('chai');

// importing pages
const LoginPage = require('../../../pages/BO/login');
const DashboardPage = require('../../../pages/BO/dashboard');
const BOBasePage = require('../../../pages/BO/BObasePage');
const ProductPage = require('../../../pages/BO/product');
const AddProductPage = require('../../../pages/BO/addProduct');
const FOProductPage = require('../../../pages/FO/product');
const ProductFaker = require('../../data/faker/product');

let page;
let loginPage;
let dashboardPage;
let boBasePage;
let productPage;
let addProductPage;
let foProductPage;
let productData;
let editedProductData;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  loginPage = await (new LoginPage(page));
  dashboardPage = await (new DashboardPage(page));
  boBasePage = await (new BOBasePage(page));
  productPage = await (new ProductPage(page));
  addProductPage = await (new AddProductPage(page));
  foProductPage = await (new FOProductPage(page));
  productData = await (new ProductFaker());
  editedProductData = await (new ProductFaker());
};

// Create, read, update and delete Standard product in BO
global.scenario('Create, read, update and delete Standard product in BO', async () => {
  test('should login in BO', async () => {
    await loginPage.goTo(global.URL_BO);
    await loginPage.login(global.EMAIL, global.PASSWD);
    const pageTitle = await dashboardPage.getPageTitle();
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
    await boBasePage.closeOnboardingModal();
  });
  test('should go to Products page', async () => {
    await boBasePage.goToSubMenu(boBasePage.productsParentLink, boBasePage.productsLink);
    const pageTitle = await productPage.getPageTitle();
    await expect(pageTitle).to.contains(productPage.pageTitle);
  });
  test('should reset all filters', async () => {
    if (await productPage.elementVisible(productPage.filterResetButton, 2000)) await productPage.resetFilter();
    await productPage.resetFilterCategory();
    const numberOfProducts = await productPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });
  test('should create Product', async () => {
    await productPage.goToAddProductPage();
    const createProductMessage = await addProductPage.createEditProduct(productData);
    await expect(createProductMessage).to.equal('Settings updated.');
  });
  test('should preview and check product in FO', async () => {
    foProductPage.page = await addProductPage.previewProduct();
    await foProductPage.checkProduct(productData);
    addProductPage.page = await foProductPage.closePage(1);
  });
  test('should edit Product', async () => {
    const createProductMessage = await addProductPage.createEditProduct(editedProductData, false);
    await expect(createProductMessage).to.equal('Settings updated.');
  });
  test('should preview and check product in FO', async () => {
    foProductPage.page = await addProductPage.previewProduct();
    await foProductPage.checkProduct(editedProductData);
    addProductPage.page = await foProductPage.closePage(1);
  });
  test('should delete Product and be on product list page', async () => {
    const testResult = await addProductPage.deleteProduct();
    await expect(testResult).to.equal(productPage.productDeletedSuccessfulMessage);
    const pageTitle = await productPage.getPageTitle();
    await expect(pageTitle).to.contains(productPage.pageTitle);
  });
}, init, true);
