require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_productsStock_enableDeliveryTimeOutOfStockProducts';
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
const FOProductPage = require('@pages/FO/product');
const FOHomePage = require('@pages/FO/home');
const SearchResultsPage = require('@pages/FO/searchResults');
// Importing data
const ProductFaker = require('@data/faker/product');

let browser;
let page;
const productData = new ProductFaker({type: 'Standard product', quantity: 0});

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
    foProductPage: new FOProductPage(page),
    foHomePage: new FOHomePage(page),
    searchResultsPage: new SearchResultsPage(page),
  };
};

describe('Enable delivery time out-of-stocks products', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO
  loginCommon.loginBO();

  describe('Create a product', async () => {
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
      const validationMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
      await expect(validationMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
    });
  });

  describe('Enable delivery time out-of-stock', () => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.shopParametersParentLink,
        this.pageObjects.boBasePage.productSettingsLink,
      );
      const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
    });

    const tests = [
      {args: {action: 'enable', enable: true, deliveryTimeText: '8-9 days'}},
      {args: {action: 'disable', enable: false, deliveryTimeText: ''}},
    ];
    tests.forEach((test, index) => {
      describe(`Check delivery time of out-of-stock products ${test.args.enable} status`, async () => {
        it(`should ${test.args.action} delivery time of out-of-stock products in BO`, async function () {
          await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);
          await this.pageObjects.productSettingsPage.setAllowOrderingOutOfStockStatus(test.args.enable);
          const result = await this.pageObjects.productSettingsPage.setDeliveryTimeOutOfStock(
            test.args.deliveryTimeText,
          );
          await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
        });

        it('should view my shop', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);
          page = await this.pageObjects.productSettingsPage.viewMyShop();
          this.pageObjects = await init();
          await this.pageObjects.foHomePage.changeLanguage('en');
          const isFoHomePage = await this.pageObjects.foHomePage.isHomePage();
          await expect(isFoHomePage, 'Fail to open FO home page').to.be.true;
        });

        it('should check delivery time block visibility', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockVisible${index}`, baseContext);
          await this.pageObjects.foHomePage.searchProduct(productData.name);
          await this.pageObjects.searchResultsPage.goToProductPage(1);
          const isDeliveryTimeBlockVisible = await this.pageObjects.foProductPage.isDeliveryInformationVisible();
          await expect(isDeliveryTimeBlockVisible).to.equal(test.args.enable);
        });

        if (test.args.enable) {
          it('should check delivery time text', async function () {
            await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockText${index}`, baseContext);
            const deliveryTimeText = await this.pageObjects.foProductPage.getDeliveryInformationText();
            await expect(deliveryTimeText).to.equal(test.args.deliveryTimeText);
          });
        }

        it('should go back to BO', async function () {
          await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);
          page = await this.pageObjects.foProductPage.closePage(browser, 1);
          this.pageObjects = await init();
          const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
          await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
        });
      });
    });
  });

  describe('Delete the product created for test ', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDeleteProduct', baseContext);
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.catalogParentLink,
        this.pageObjects.boBasePage.productsLink,
      );
      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });

    it('should delete product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteProduct', baseContext);
      const deleteTextResult = await this.pageObjects.productsPage.deleteProduct(productData);
      await expect(deleteTextResult).to.equal(this.pageObjects.productsPage.productDeletedSuccessfulMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAllFilters', baseContext);
      await this.pageObjects.productsPage.resetFilterCategory();
      const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      await expect(numberOfProducts).to.be.above(0);
    });
  });
});
