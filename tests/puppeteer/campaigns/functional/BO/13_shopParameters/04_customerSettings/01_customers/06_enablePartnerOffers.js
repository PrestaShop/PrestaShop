require('module-alias/register');
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customerSettings_customers_enablePartnerOffer';
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomerSettingsPage = require('@pages/BO/shopParameters/customerSettings');
const {options} = require('@pages/BO/shopParameters/customerSettings/options');
const FOBasePage = require('@pages/FO/FObasePage');
const LoginFOPage = require('@pages/FO/login');
// Importing data

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customerSettingsPage: new CustomerSettingsPage(page),
    foBasePage: new FOBasePage(page),
    loginFOPage: new LoginFOPage(page),
  };
};

describe('Enable partner offer', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });

  // Login into BO and go to customer settings page
  loginCommon.loginBO();

  it('should go to \'Shop parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.shopParametersParentLink,
      this.pageObjects.boBasePage.customerSettingsLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.customerSettingsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customerSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'disable', enable: false}},
    {args: {action: 'enable', enable: true}},
  ];
  tests.forEach((test) => {
    it(`should ${test.args.action} partner offer`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}PartnerOffer`, baseContext);
      const result = await this.pageObjects.customerSettingsPage.setOptionStatus(
        options.OPTION_PARTNER_OFFER,
        test.args.enable,
      );
      await expect(result).to.contains(this.pageObjects.customerSettingsPage.successfulUpdateMessage);
    });

    it('should go to create customer account in FO and check partner offer checkbox', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkIsPartnerOffer${this.pageObjects.boBasePage.uppercaseFirstCharacter(test.args.action)}`,
        baseContext,
      );
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      await this.pageObjects.foBasePage.goToLoginPage();
      await this.pageObjects.loginFOPage.goToCreateAccountPage();
      const isPartnerOfferVisible = await this.pageObjects.loginFOPage.isPartnerOfferVisible();
      await expect(isPartnerOfferVisible).to.be.equal(test.args.enable);
      page = await this.pageObjects.loginFOPage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
});
