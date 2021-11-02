require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const customerSettingsPage = require('@pages/BO/shopParameters/customerSettings');
const {options} = require('@pages/BO/shopParameters/customerSettings/options');

// Import FO pages
const foHomePage = require('@pages/FO/home');
const loginFOPage = require('@pages/FO/login');
const foCreateAccountPage = require('@pages/FO/myAccount/add');

const baseContext = 'functional_BO_shopParameters_customerSettings_customers_enableB2BMode';

let browserContext;
let page;

/*
Enable B2B mode
Go to FO > create account page and check that company input is visible
Disable B2B mode
Go to FO > create account page and check that company input is not visible
 */
describe('BO - Shop Parameters - Customer Settings : Enable/Disable B2B mode', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Shop parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.shopParametersParentLink,
      dashboardPage.customerSettingsLink,
    );

    await customerSettingsPage.closeSfToolBar(page);

    const pageTitle = await customerSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', enable: true}},
    {args: {action: 'disable', enable: false}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} B2B mode`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}B2BMode`, baseContext);

      const result = await customerSettingsPage.setOptionStatus(
        page,
        options.OPTION_B2B,
        test.args.enable,
      );

      await expect(result).to.contains(customerSettingsPage.successfulUpdateMessage);
    });

    it('should go to create customer page in FO and check company input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkB2BMode${index}`, baseContext);

      // Go to FO and change language
      page = await customerSettingsPage.viewMyShop(page);
      await foHomePage.changeLanguage(page, 'en');

      // Go to create account page
      await foHomePage.goToLoginPage(page);
      await loginFOPage.goToCreateAccountPage(page);

      // Check B2B mode
      const isCompanyInputVisible = await foCreateAccountPage.isCompanyInputVisible(page);
      await expect(isCompanyInputVisible).to.be.equal(test.args.enable);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await foCreateAccountPage.closePage(browserContext, page, 0);

      const pageTitle = await customerSettingsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customerSettingsPage.pageTitle);
    });
  });
});
