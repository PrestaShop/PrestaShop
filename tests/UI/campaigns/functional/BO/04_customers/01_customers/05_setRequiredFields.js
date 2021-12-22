require('module-alias/register');

const {expect} = require('chai');

// Helpers to open and close browser
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const customersPage = require('@pages/BO/customers');

// Import FO pages
const foLoginPage = require('@pages/FO/login');
const foHomePage = require('@pages/FO/home');
const foCreateAccountPage = require('@pages/FO/myAccount/add');

const baseContext = 'functional_BO_customers_customers_setRequiredFields';

let browserContext;
let page;

describe('BO - Customers - Customers : Set required fields', async () => {
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

  [
    {args: {action: 'select', exist: true}},
    {args: {action: 'unselect', exist: false}},
  ].forEach((test, index) => {
    it(`should ${test.args.action} 'Partner offers' as required fields`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}PartnersOffers`, baseContext);

      const textResult = await customersPage.setRequiredFields(page, 0, test.args.exist);
      await expect(textResult).to.equal(customersPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // View shop
      page = await customersPage.viewMyShop(page);

      // Change language in FO
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      await expect(isHomePage, 'Fail to open FO home page').to.be.true;
    });

    it('should go to create account FO and check \'Receive offers from our partners\' checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkPartnersOffers${index}`, baseContext);

      // Go to create account page
      await foHomePage.goToLoginPage(page);
      await foLoginPage.goToCreateAccountPage(page);

      const pageTitle = await foCreateAccountPage.getPageTitle(page);
      await expect(pageTitle).to.contains(foCreateAccountPage.pageTitle);
    });

    it('should check \'Receive offers from our partners\' checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkReceiveOffersCheckbox${index}`, baseContext);

      // Check partner offer required
      const isPartnerOfferRequired = await foCreateAccountPage.isPartnerOfferRequired(page);
      await expect(isPartnerOfferRequired).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await foCreateAccountPage.closePage(browserContext, page, 0);

      const pageTitle = await customersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(customersPage.pageTitle);
    });
  });
});
