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
let productData;

// creating pages objects in a function
const init = async () => {
  page = await global.browser.newPage();
  loginPage = await (new LoginPage(page));
  dashboardPage = await (new DashboardPage(page));
  boBasePage = await (new BOBasePage(page));
  productsPage = await (new ProductsPage(page));
  addProductPage = await (new AddProductPage(page));
  const productToCreate = {
    type: 'Standard product',
    productHasCombinations: false,
  };
  productData = await (new ProductFaker(productToCreate));
};

// Create Standard product in BO and Delete it with DropDown Menu
global.scenario('Create Standard product in BO and Delete it with DropDown Menu', async () => {
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
    await expect(createProductMessage).to.equal('Settings updated.');
  });
  test('should go to Products page', async () => {
    await boBasePage.goToSubMenu(boBasePage.productsParentLink, boBasePage.productsLink);
    const pageTitle = await productsPage.getPageTitle();
    await expect(pageTitle).to.contains(productsPage.pageTitle);
  });
  test('should delete product with from DropDown Menu', async () => {
    const deleteTextResult = await productsPage.deleteProduct(productData);
    await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
  });
  test('should reset all filters', async () => {
    if (await productsPage.elementVisible(productsPage.filterResetButton, 2000)) await productsPage.resetFilter();
    await productsPage.resetFilterCategory();
    const numberOfProducts = await productsPage.getNumberOfProductsFromList();
    await expect(numberOfProducts).to.be.above(0);
  });
}, init, true);
