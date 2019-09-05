// Using chai
const {expect} = require('chai');

// importing pages
const LoginPage = require('../../../pages/BO/login');
const DashboardPage = require('../../../pages/BO/dashboard');
const BOBasePage = require('../../../pages/BO/BObasePage');
const ProductsPage = require('../../../pages/BO/products');
const AddProductPage = require('../../../pages/BO/addProduct');
const ProductFaker = require('../../data/faker/product');

let page;
let loginPage;
let dashboardPage;
let boBasePage;
let productsPage;
let addProductPage;
let firstProductData;
let secondProductData;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  loginPage = await (new LoginPage(page));
  dashboardPage = await (new DashboardPage(page));
  boBasePage = await (new BOBasePage(page));
  productsPage = await (new ProductsPage(page));
  addProductPage = await (new AddProductPage(page));
  const productToCreate = {
    name: 'product To Delete 1',
    type: 'Standard product',
    productHasCombinations: false,
  };
  firstProductData = await (new ProductFaker(productToCreate));
  productToCreate.name = 'product To Delete 2';
  secondProductData = await (new ProductFaker(productToCreate));
};

// Create 2 Standard products in BO and Delete it with Bulk Actions
global.scenario('Create Standard product in BO and Delete it with Bulk Actions', async () => {
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
  test('should create First Product', async () => {
    await productsPage.goToAddProductPage();
    const createProductMessage = await addProductPage.createEditProduct(firstProductData);
    await expect(createProductMessage).to.equal('Settings updated.');
  });
  test('should go to Products page', async () => {
    await boBasePage.goToSubMenu(boBasePage.productsParentLink, boBasePage.productsLink);
    const pageTitle = await productsPage.getPageTitle();
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });
  test('should create Second Product', async () => {
    await productsPage.goToAddProductPage();
    const createProductMessage = await addProductPage.createEditProduct(secondProductData);
    await expect(createProductMessage).to.equal('Settings updated.');
  });
  test('should go to Products page', async () => {
    await boBasePage.goToSubMenu(boBasePage.productsParentLink, boBasePage.productsLink);
    const pageTitle = await productsPage.getPageTitle();
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });
  test('should delete products with bulk Actions', async () => {
    // Filter By reference first
    await productsPage.filterProducts('name', 'product To Delete ');
    const deleteTextResult = await productsPage.deleteAllProductsWithBulkActions();
    await expect(deleteTextResult).to.equal(productsPage.productMultiDeletedSuccessfulMessage);
  });
  test('should reset all filters', async () => {
    if (await productsPage.elementVisible(productsPage.filterResetButton, 2000)) await productsPage.resetFilter();
    await productsPage.resetFilterCategory();
    const numberOfProducts = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });
}, init, true);
