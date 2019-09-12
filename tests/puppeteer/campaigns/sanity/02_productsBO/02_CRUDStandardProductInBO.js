// Using chai
const {expect} = require('chai');

// importing pages
const LoginPage = require('../../../pages/BO/login');
const DashboardPage = require('../../../pages/BO/dashboard');
const BOBasePage = require('../../../pages/BO/BObasePage');
const ProductsPage = require('../../../pages/BO/products');
const AddProductPage = require('../../../pages/BO/addProduct');
const FOProductPage = require('../../../pages/FO/product');
const ProductFaker = require('../../data/faker/product');

let page;
let loginPage;
let dashboardPage;
let boBasePage;
let productsPage;
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
  productsPage = await (new ProductsPage(page));
  addProductPage = await (new AddProductPage(page));
  foProductPage = await (new FOProductPage(page));
  const productToCreate = {
    type: 'Standard product',
    productHasCombinations: false,
  };
  productData = await (new ProductFaker(productToCreate));
  editedProductData = await (new ProductFaker(productToCreate));
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
    const pageTitle = await productsPage.getPageTitle();
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });
  test('should reset all filters', async () => {
    if (await productsPage.elementVisible(productsPage.filterResetButton, 2000)) await productsPage.resetFilter();
    await productsPage.resetFilterCategory();
    const numberOfProducts = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });
  test('should create Product', async () => {
    await productsPage.goToAddProductPage();
    const createProductMessage = await addProductPage.createEditProduct(productData);
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });
  test('should preview and check product in FO', async () => {
    foProductPage.page = await addProductPage.previewProduct();
    await foProductPage.checkProduct(productData);
    addProductPage.page = await foProductPage.closePage(1);
  });
  test('should edit Product', async () => {
    const createProductMessage = await addProductPage.createEditProduct(editedProductData, false);
    await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
  });
  test('should preview and check product in FO', async () => {
    foProductPage.page = await addProductPage.previewProduct();
    await foProductPage.checkProduct(editedProductData);
    addProductPage.page = await foProductPage.closePage(1);
  });
  test('should delete Product and be on product list page', async () => {
    const testResult = await addProductPage.deleteProduct();
    await expect(testResult).to.equal(productsPage.productDeletedSuccessfulMessage);
    const pageTitle = await productsPage.getPageTitle();
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });
}, init, true);
