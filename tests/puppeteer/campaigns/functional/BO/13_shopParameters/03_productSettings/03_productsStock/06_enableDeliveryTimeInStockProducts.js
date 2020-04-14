require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_productSettings_productsStock_enableDeliveryTimeInStockProducts';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductSettingsPage = require('@pages/BO/shopParameters/productSettings');
const FOProductPage = require('@pages/FO/product');
const FOHomePage = require('@pages/FO/home');

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    foProductPage: new FOProductPage(page),
    foHomePage: new FOHomePage(page),
  };
};

describe('Enable delivery time in stocks products', async () => {
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
    {args: {action: 'enable', enable: true, deliveryTimeText: '3-4 days'}},
    {args: {action: 'disable', enable: false, deliveryTimeText: ''}},
  ];
  tests.forEach((test, index) => {
    it(`should ${test.args.action} delivery time of in-stock products`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}StockManagement`, baseContext);
      const result = await this.pageObjects.productSettingsPage.setDeliveryTimeInStock(test.args.deliveryTimeText);
      await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);
      page = await this.pageObjects.productSettingsPage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foHomePage.changeLanguage('en');
      const isfoHomePage = await this.pageObjects.foHomePage.isHomePage();
      await expect(isfoHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should check delivery time block visibility', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `deliveryTimeBlockVisible${index}`, baseContext);
      await this.pageObjects.foHomePage.goToProductPage(4);
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
