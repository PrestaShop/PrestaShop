require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customerSettingsPage = require('@pages/BO/shopParameters/customerSettings');
const {options} = require('@pages/BO/shopParameters/customerSettings/options');
const foHomePage = require('@pages/FO/home');
const loginFOPage = require('@pages/FO/login');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_customerSettings_customers_askForBirthDate';

let browserContext;
let page;

describe('Enable ask for birth date', async () => {
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
    {args: {action: 'disable', enable: false}},
    {args: {action: 'enable', enable: true}},
  ];

  tests.forEach((test) => {
    it(`should ${test.args.action} ask for birth date`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}AskForBirthDate`, baseContext);

      const result = await customerSettingsPage.setOptionStatus(
        page,
        options.OPTION_BIRTH_DATE,
        test.args.enable,
      );

      await expect(result).to.contains(customerSettingsPage.successfulUpdateMessage);
    });

    it('should go to customer account in FO and check birth day input', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkIsBirthDate${customerSettingsPage.uppercaseFirstCharacter(test.args.action)}`,
        baseContext,
      );

      // Go to FO
      page = await customerSettingsPage.viewMyShop(page);

      // Change language in FO
      await foHomePage.changeLanguage(page, 'en');

      // Go to create account page
      await foHomePage.goToLoginPage(page);
      await loginFOPage.goToCreateAccountPage(page);

      // Check birthday
      const isBirthDateInputVisible = await loginFOPage.isBirthDateVisible(page);
      await expect(isBirthDateInputVisible).to.be.equal(test.args.enable);

      // Go back to BO
      page = await loginFOPage.closePage(browserContext, page, 0);
    });
  });
});
