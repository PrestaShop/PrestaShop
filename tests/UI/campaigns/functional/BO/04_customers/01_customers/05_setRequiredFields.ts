import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomersPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  foClassicCreateAccountPage,
  foClassicHomePage,
  foClassicLoginPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_customers_customers_setRequiredFields';

describe('BO - Customers - Customers : Set required fields', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Customers > Customers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.customersParentLink,
      boDashboardPage.customersLink,
    );
    await boCustomersPage.closeSfToolBar(page);

    const pageTitle = await boCustomersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomersPage.pageTitle);
  });

  [
    {args: {action: 'select', exist: true}},
    {args: {action: 'unselect', exist: false}},
  ].forEach((test, index) => {
    it(`should ${test.args.action} 'Partner offers' as required fields`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}PartnersOffers`, baseContext);

      const textResult = await boCustomersPage.setRequiredFields(page, 0, test.args.exist);
      expect(textResult).to.equal(boCustomersPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop${index}`, baseContext);

      // View shop
      page = await boCustomersPage.viewMyShop(page);
      // Change language in FO
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to create account FO and check \'Receive offers from our partners\' checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkPartnersOffers${index}`, baseContext);

      // Go to create account page
      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.goToCreateAccountPage(page);

      const pageTitle = await foClassicCreateAccountPage.getHeaderTitle(page);
      expect(pageTitle).to.contains(foClassicCreateAccountPage.formTitle);
    });

    it('should check \'Receive offers from our partners\' checkbox', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkReceiveOffersCheckbox${index}`, baseContext);

      // Check partner offer required
      const isPartnerOfferRequired = await foClassicCreateAccountPage.isPartnerOfferRequired(page);
      expect(isPartnerOfferRequired).to.be.equal(test.args.exist);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await foClassicCreateAccountPage.closePage(browserContext, page, 0);

      const pageTitle = await boCustomersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomersPage.pageTitle);
    });
  });
});
