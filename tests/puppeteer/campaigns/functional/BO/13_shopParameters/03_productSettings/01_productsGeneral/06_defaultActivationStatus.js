require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_enableDisableDefaultActivationStatus';
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
// Importing data

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
  };
};

/*
Enable default activation status
Check that a new product is online by default
Disable default activation status
Check that a new product is offline by default
 */
describe('Enable/Disable default activation status', async () => {
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
  const tests = [
    {args: {action: 'enable', enable: true}},
    {args: {action: 'disable', enable: false}},
  ];
  tests.forEach((test) => {
    it('should go to \'Shop parameters > Product Settings\' page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToProductSettingsPageTo${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}Status`,
        baseContext,
      );
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.shopParametersParentLink,
        this.pageObjects.boBasePage.productSettingsLink,
      );
      await this.pageObjects.boBasePage.closeSfToolBar();
      const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
    });

    it(`should ${test.args.action} default activation status`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${test.args.action}DefaultActivationStatus`,
        baseContext,
      );
      const result = await this.pageObjects.productSettingsPage.setDefaultActivationStatus(test.args.enable);
      await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
    });

    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToProductsPageToCheck${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}Status`,
        baseContext,
      );
      await this.pageObjects.boBasePage.goToSubMenu(
        this.pageObjects.boBasePage.catalogParentLink,
        this.pageObjects.boBasePage.productsLink,
      );
      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });

    it('should go to create product page and check the new product online status', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `goToAddProductPageToCheck${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}Status`,
        baseContext,
      );
      await this.pageObjects.productsPage.goToAddProductPage();
      const online = await this.pageObjects.addProductPage.getOnlineButtonStatus();
      await expect(online).to.be.equal(test.args.enable);
    });
  });
});
