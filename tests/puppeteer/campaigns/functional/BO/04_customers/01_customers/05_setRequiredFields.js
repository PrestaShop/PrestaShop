require('module-alias/register');
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
// Importing pages
const BOBasePage = require('@pages/BO/BObasePage');
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const CustomersPage = require('@pages/BO/customers');
const FOLoginPage = require('@pages/FO/login');
const FOBasePage = require('@pages/FO/FObasePage');
const LoginFOPage = require('@pages/FO/login');
// Test context imports
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_setRequiredFields';

let browser;
let page;

// Init objects needed
const init = async function () {
  return {
    boBasePage: new BOBasePage(page),
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    customersPage: new CustomersPage(page),
    foLoginPage: new FOLoginPage(page),
    foBasePage: new FOBasePage(page),
    loginFOPage: new LoginFOPage(page),
  };
};

describe('Set required fields for customers', async () => {
  // before and after functions
  before(async function () {
    browser = await helper.createBrowser();
    page = await helper.newTab(browser);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowser(browser);
  });
  // Login into BO and go to customers page
  loginCommon.loginBO();

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);
    await this.pageObjects.boBasePage.goToSubMenu(
      this.pageObjects.boBasePage.customersParentLink,
      this.pageObjects.boBasePage.customersLink,
    );
    await this.pageObjects.boBasePage.closeSfToolBar();
    const pageTitle = await this.pageObjects.customersPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.customersPage.pageTitle);
  });

  const tests = [
    {args: {action: 'select', exist: true}},
    {args: {action: 'unselect', exist: false}},
  ];
  tests.forEach((test) => {
    it(`should ${test.args.action} 'Partner offers' as required fields`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}PartnersOffers`, baseContext);
      const textResult = await this.pageObjects.customersPage.setRequiredFields(0, test.args.exist);
      await expect(textResult).to.equal(this.pageObjects.customersPage.successfulUpdateMessage);
    });

    it('should go to create account FO and check \'Receive offers from our partners\' checkbox', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkPartnersOffersCheckboxRequired_${test.args.exist}`,
        baseContext,
      );
      page = await this.pageObjects.boBasePage.viewMyShop();
      this.pageObjects = await init();
      await this.pageObjects.foBasePage.changeLanguage('en');
      await this.pageObjects.foBasePage.goToLoginPage();
      await this.pageObjects.loginFOPage.goToCreateAccountPage();
      const isPartnerOfferRequired = await this.pageObjects.loginFOPage.isPartnerOfferRequired();
      await expect(isPartnerOfferRequired).to.be.equal(test.args.exist);
      page = await this.pageObjects.loginFOPage.closePage(browser, 1);
      this.pageObjects = await init();
    });
  });
});
