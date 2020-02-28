require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_displayRemainingQuantitiesOnProductPage';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductSettingsPage = require('@pages/BO/shopParameters/productSettings');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const ProductPage = require('@pages/FO/product');
const HomePage = require('@pages/FO/home');
const SearchResultsPage = require('@pages/FO/searchResults');
// Importing data
const ProductFaker = require('@data/faker/product');

let browser;
let page;
const productData = new ProductFaker({type: 'Standard product', quantity: 2});
const remainingQuantity = 0;
const defaultRemainingQuantity = 3;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    searchResultsPage: new SearchResultsPage(page),
  };
};

/*
Create product quantity 2
Update display remaining quantities to 0
Go to FO product page and check that the product availability is not displayed
Update display remaining quantities to the default value
Go to FO product page and check that the product availability is displayed
 */
describe('Test display remaining quantities', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to product settings page
  loginCommon.loginBO();

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.catalogParentLink,
      this.pageObjects.boBasePage.productsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should go to create product page and create a product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'createProduct', baseContext);
    await this.pageObjects.productsPage.goToAddProductPage();
    const validationMessage = await this.pageObjects.addProductPage.createEditProduct(productData);
    await expect(validationMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
  });

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.productSettingsLink,
    );
    const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
  });

  it('should update Display remaining quantities to 0', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setDisplayRemainingQuantity', baseContext);
    const result = await this.pageObjects.productSettingsPage.setDisplayRemainingQuantities(remainingQuantity);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should check that the product availability is not displayed in FO product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkThatRemainingQuantityAlertNotVisible', baseContext);
    page = await this.pageObjects.productSettingsPage.viewMyShop();
    this.pageObjects = await init();
    await this.pageObjects.homePage.searchProduct(productData.name);
    await this.pageObjects.searchResultsPage.goToProductPage(1);
    const lastQuantityIsVisible = await this.pageObjects.productPage.isAvailabilityQuantityDisplayed();
    await expect(lastQuantityIsVisible).to.be.false;
    page = await this.pageObjects.productPage.closePage(browser, 1);
    this.pageObjects = await init();
  });

  it('should update Display remaining quantities to the default value', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setDisplayRemainingQuantityDefaultValue', baseContext);
    const result = await this.pageObjects.productSettingsPage.setDisplayRemainingQuantities(defaultRemainingQuantity);
    await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
  });

  it('should check that the product availability is displayed in FO product page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkThatRemainingQuantityAlertIsVisible', baseContext);
    page = await this.pageObjects.productSettingsPage.viewMyShop();
    this.pageObjects = await init();
    await this.pageObjects.homePage.searchProduct(productData.name);
    await this.pageObjects.searchResultsPage.goToProductPage(1);
    this.pageObjects = await init();
    const lastQuantityIsVisible = await this.pageObjects.productPage.isAvailabilityQuantityDisplayed();
    await expect(lastQuantityIsVisible).to.be.true;
    page = await this.pageObjects.productPage.closePage(browser, 1);
    this.pageObjects = await init();
  });
});
