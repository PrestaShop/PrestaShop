require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductSettingsPage = require('@pages/BO/shopParameters/productSettings');
const HomePage = require('@pages/FO/home');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_productSettings_productsGeneral_newDaysNumber';

let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productSettingsPage: new ProductSettingsPage(page),
    homePage: new HomePage(page),
  };
};

/*
Update new days number to 0
Check that there is no new products in FO
Go back to the default value
Check that all products are new in FO
 */
describe('Number of days for which the product is considered \'new\'', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to product settings page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > Product Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductSettingsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.productSettingsLink,
    );

    await this.pageObjects.productSettingsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.productSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productSettingsPage.pageTitle);
  });

  const tests = [
    {args: {value: 0, exist: false, state: 'NotVisible'}},
    {args: {value: 20, exist: true, state: 'Visible'}},
  ];

  tests.forEach((test) => {
    it(`should update Number of days to ${test.args.value}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `updateNumberOfDaysTo${test.args.value}`, baseContext);

      const result = await this.pageObjects.productSettingsPage.updateNumberOfDays(test.args.value);
      await expect(result).to.contains(this.pageObjects.productSettingsPage.successfulUpdateMessage);
    });

    it('should check the new flag in the product miniature in FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkIfNewFlagIs${test.args.state}`, baseContext);

      page = await this.pageObjects.productSettingsPage.viewMyShop();
      this.pageObjects = await init();

      const isNewFlagVisible = await this.pageObjects.homePage.isNewFlagVisible(1);
      await expect(isNewFlagVisible).to.be.equal(test.args.exist);

      page = await this.pageObjects.homePage.closePage(browserContext, 0);
      this.pageObjects = await init();
    });
  });
});
