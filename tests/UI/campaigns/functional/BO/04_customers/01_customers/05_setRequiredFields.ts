// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import customersPage from '@pages/BO/customers';
import dashboardPage from '@pages/BO/dashboard';
// Import FO pages
import {homePage as foHomePage} from '@pages/FO/home';
import {loginPage as foLoginPage} from '@pages/FO/login';
import {createAccountPage as foCreateAccountPage} from '@pages/FO/myAccount/add';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_customers_customers_setRequiredFields';

describe('BO - Customers - Customers : Set required fields', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    expect(pageTitle).to.contains(customersPage.pageTitle);
  });

  [
    {args: {action: 'select', exist: true}},
    {args: {action: 'unselect', exist: false}},
  ].forEach((test, index) => {
    it(`should ${test.args.action} 'Partner offers' as required fields`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}PartnersOffers`, baseContext);

      const textResult = await customersPage.setRequiredFields(page, 0, test.args.exist);
      expect(textResult).to.equal(customersPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // View shop
      page = await customersPage.viewMyShop(page);
      // Change language in FO
      await foHomePage.changeLanguage(page, 'en');

      const isHomePage = await foHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to create account FO and check \'Receive offers from our partners\' checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkPartnersOffers${index}`, baseContext);

      // Go to create account page
      await foHomePage.goToLoginPage(page);
      await foLoginPage.goToCreateAccountPage(page);

      const pageTitle = await foCreateAccountPage.getHeaderTitle(page);
      expect(pageTitle).to.contains(foCreateAccountPage.formTitle);
    });

    it('should check \'Receive offers from our partners\' checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkReceiveOffersCheckbox${index}`, baseContext);

      // Check partner offer required
      const isPartnerOfferRequired = await foCreateAccountPage.isPartnerOfferRequired(page);
      expect(isPartnerOfferRequired).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await foCreateAccountPage.closePage(browserContext, page, 0);

      const pageTitle = await customersPage.getPageTitle(page);
      expect(pageTitle).to.contains(customersPage.pageTitle);
    });
  });
});
