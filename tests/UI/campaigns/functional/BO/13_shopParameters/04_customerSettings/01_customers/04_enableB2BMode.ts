import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boCustomerSettingsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  foClassicCreateAccountPage,
  foClassicHomePage,
  foClassicLoginPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_customerSettings_customers_enableB2BMode';

/*
Enable B2B mode
Go to FO > create account page and check that company input is visible
Disable B2B mode
Go to FO > create account page and check that company input is not visible
 */
describe('BO - Shop Parameters - Customer Settings : Enable/Disable B2B mode', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
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

  it('should go to \'Shop parameters > Customer Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCustomerSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shopParametersParentLink,
      boDashboardPage.customerSettingsLink,
    );

    await boCustomerSettingsPage.closeSfToolBar(page);

    const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
  });

  const tests = [
    {args: {action: 'enable', enable: true}},
    {args: {action: 'disable', enable: false}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} B2B mode`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}B2BMode`, baseContext);

      const result = await boCustomerSettingsPage.setOptionStatus(
        page,
        boCustomerSettingsPage.OPTION_B2B,
        test.args.enable,
      );
      expect(result).to.contains(boCustomerSettingsPage.successfulUpdateMessage);
    });

    it('should view my shop', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `viewMyShop_${index}`, baseContext);

      // Go to FO
      page = await boCustomerSettingsPage.viewMyShop(page);
      await foClassicHomePage.changeLanguage(page, 'en');

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage, 'Fail to open FO home page').to.eq(true);
    });

    it('should go to create customer page in FO and check company input', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkB2BMode${index}`, baseContext);

      // Go to create account page
      await foClassicHomePage.goToLoginPage(page);
      await foClassicLoginPage.goToCreateAccountPage(page);

      // Check B2B mode
      const isCompanyInputVisible = await foClassicCreateAccountPage.isCompanyInputVisible(page);
      expect(isCompanyInputVisible).to.be.equal(test.args.enable);
    });

    it('should go back to BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goBackToBO${index}`, baseContext);

      // Go back to BO
      page = await foClassicCreateAccountPage.closePage(browserContext, page, 0);

      const pageTitle = await boCustomerSettingsPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCustomerSettingsPage.pageTitle);
    });
  });
});
