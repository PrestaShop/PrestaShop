require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const GeneralPage = require('@pages/BO/shopParameters/general');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParameters_general_general_enableDisableMultiStore';

let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    generalPage: new GeneralPage(page),
  };
};

describe('Enable/Disable multi store', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to general page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > General\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.shopParametersParentLink,
      this.pageObjects.dashboardPage.shopParametersGeneralLink,
    );

    await this.pageObjects.generalPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.generalPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.generalPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', exist: true}},
    {args: {action: 'disable', exist: false}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} multi store`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}MultiStore`, baseContext);

      const result = await this.pageObjects.generalPage.setMultiStoreStatus(test.args.exist);
      await expect(result).to.contains(this.pageObjects.generalPage.successfulUpdateMessage);
    });

    it('should check the existence of \'Advanced Parameters > Multistore\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToMultiStorePage_${index}`, baseContext);

      const result = await this.pageObjects.generalPage.isSubmenuVisible(
        this.pageObjects.generalPage.advancedParametersLink,
        this.pageObjects.generalPage.multistoreLink,
      );

      await expect(result).to.be.equal(test.args.exist);
    });
  });
});
