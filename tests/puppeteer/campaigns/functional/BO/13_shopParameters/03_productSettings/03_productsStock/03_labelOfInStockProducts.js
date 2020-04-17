require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_productsStock_labelOfInStockProducts';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductSettingsPage = require('@pages/BO/shopParameters/productSettings');
const ProductPage = require('@pages/FO/product');
const HomePage = require('@pages/FO/home');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
  };
};

describe('Update label of in-stock products', async () => {
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
    {args: {label: 'Product is available', labelToCheck: 'Product is available', exist: true}},
    {args: {label: ' ', labelToCheck: '', exist: false}},
  ];
  tests.forEach((test, index) => {
    it(`should set '${test.args.label}' in Label of in-stock products input`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateLabelOfInStockProducts_${index}`, baseContext);
      const result = await this.pageObjects.productSettingsPage.setLabelOfInStockProducts(test.args.label);
      await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
    });

    it('should check the label of in-stock product in FO product page', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkLabelInStock_${index}`,
        baseContext,
      );
      page = await this.pageObjects.productSettingsPage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.homePage.goToProductPage(1);
      const isVisible = await this.pageObjects.productPage.isAvailabilityQuantityDisplayed();
      await expect(isVisible).to.be.equal(test.args.exist);
      const availabilityLabel = await this.pageObjects.productPage.getProductAvailabilityLabel();
      await expect(availabilityLabel).to.contains(test.args.labelToCheck);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBo${index}`, baseContext);
      page = await this.pageObjects.productPage.closePage(browser, 1);
      this.pageObjects = await init();
      const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
    });
  });
});
