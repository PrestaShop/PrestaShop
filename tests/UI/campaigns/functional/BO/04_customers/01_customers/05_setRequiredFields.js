require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');
const foCreateAccountPage = require('@pages/FO/myAccount/add');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_customers_customers_setRequiredFields';


let browserContext;
let page;

describe('Set required fields for customers', async () => {
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

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.customersParentLink,
      dashboardPage.customersLink,
    );

    await customersPage.closeSfToolBar(page);

    const pageTitle = await customersPage.getPageTitle(page);
    await expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  const tests = [
    {args: {action: 'select', exist: true}},
    {args: {action: 'unselect', exist: false}},
  ];

  tests.forEach((test) => {
    it(`should ${test.args.action} 'Partner offers' as required fields`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}PartnersOffers`, baseContext);

      const textResult = await customersPage.setRequiredFields(page, 0, test.args.exist);
      await expect(textResult).to.equal(customersPage.successfulUpdateMessage);
    });

    it('should go to create account FO and check \'Receive offers from our partners\' checkbox', async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `checkPartnersOffersCheckboxRequired_${test.args.exist}`,
        baseContext,
      );

      // View shop
      page = await customersPage.viewMyShop(page);

      // Change language in FO
      await foHomePage.changeLanguage(page, 'en');

      // Go to create account page
      await foHomePage.goToLoginPage(page);
      await foLoginPage.goToCreateAccountPage(page);

      // Check partner offer required
      const isPartnerOfferRequired = await foCreateAccountPage.isPartnerOfferRequired(page);
      await expect(isPartnerOfferRequired).to.be.equal(test.args.exist);

      // Go back to BO
      page = await foCreateAccountPage.closePage(browserContext, page, 0);
    });
  });
});
