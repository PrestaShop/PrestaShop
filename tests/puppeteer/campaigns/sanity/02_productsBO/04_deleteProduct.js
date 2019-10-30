require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BOBasePage = require('@pages/BO/BObasePage');
const ProductsPage = require('@pages/BO/products');
const AddProductPage = require('@pages/BO/addProduct');
const ProductFaker = require('@data/faker/product');

let browser;
let page;
let productData;

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    boBasePage: new BOBasePage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
  };
};
// Create Standard product in BO and Delete it with DropDown Menu
describe('Create Standard product in BO and Delete it with DropDown Menu', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
    const productToCreate = {
      type: 'Standard product',
      productHasCombinations: false,
    };
    productData = await (new ProductFaker(productToCreate));
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Steps
  loginCommon.loginBO();

  it('should go to Products page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });

  it('should create Product', async function () {
    await this.pageObjects.productsPage.goToAddProductPage();
    const createProductMessage = await this.pageObjects.addProductPage.createEditProduct(productData);
    await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  it('should go to Products page', async function () {
    await this.pageObjects.boBasePage.goToSubMenu(this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink);
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should delete product with from DropDown Menu', async function () {
    const deleteTextResult = await this.pageObjects.productsPage.deleteProduct(productData);
    await expect(deleteTextResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
  });

  it('should reset all filters', async function () {
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });
});
