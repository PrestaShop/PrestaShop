require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const GeneralPage = require('@pages/BO/shopParameters/general');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_shopParams_general_general_enableDisableMultiStore';

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    generalPage: new GeneralPage(page),
  };
};

describe('Enable/Disable multi store', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to general page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > General\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToGeneralPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.shopParametersGeneralLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
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
        this.pageObjects.boBasePage.advancedParametersLink,
        this.pageObjects.boBasePage.multistoreLink,
      );
      await expect(result).to.be.equal(test.args.exist);
    });
  });
});
