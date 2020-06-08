require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductSettingsPage = require('@pages/BO/shopParameters/productSettings');
const ProductPage = require('@pages/FO/product');
const HomePage = require('@pages/FO/home');
const SearchResultsPage = require('@pages/FO/searchResults');

// Import data
const {Products} = require('@data/demo/products');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_separatorOfAttributeAnchor';

let browser;
let browserContext;
let page;

const productAttributes = ['1', 'size', 's/8', 'color', 'white'];

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    homePage: new HomePage(page),
    productPage: new ProductPage(page),
    searchResultsPage: new SearchResultsPage(page),
  };
};

describe('Update separator of attribute anchor on the product links', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    browserContext = await helper.createBrowserContext(browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to product settings page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.productSettingsLink,
    );

    const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {option: ',', attributesInProductLink: productAttributes.join(',')}},
    {args: {option: '-', attributesInProductLink: productAttributes.join('-')}},
  ];

  tests.forEach((test, index) => {
    it(`should choose the separator option '${test.args.option}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `chooseOption_${index}`, baseContext);

      const result = await this.pageObjects.productSettingsPage.setSeparatorOfAttributeOnProductLink(
        test.args.option,
      );

      await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
    });

    it('should check the attribute separator on the product links in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkAttributeSeparator_${index}`, baseContext);

      page = await this.pageObjects.productSettingsPage.viewMyShop();
      this.pageObjects = await init();

      await this.pageObjects.homePage.changeLanguage('en');

      await this.pageObjects.homePage.searchProduct(Products.demo_1.name);
      await this.pageObjects.searchResultsPage.goToProductPage(1);

      const currentURL = await this.pageObjects.productPage.getProductPageURL();
      await expect(currentURL).to.contains(test.args.attributesInProductLink);

      page = await this.pageObjects.productPage.closePage(browserContext, 0);
      this.pageObjects = await init();
    });
  });
});
