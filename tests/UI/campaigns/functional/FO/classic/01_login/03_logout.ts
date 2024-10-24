// Import utils
import testContext from '@utils/testContext';

import {
  type BrowserContext,
  dataCustomers,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

const baseContext: string = 'functional_FO_classic_login_logout';

describe('FO - Login : Logout from FO', async () => {
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

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

    await foClassicHomePage.goTo(page, global.FO.URL);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginPage', baseContext);

    await foClassicHomePage.goToLoginPage(page);

    const pageTitle = await foClassicLoginPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
  });

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFO1', baseContext);

    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected').to.eq(true);
  });

  it('should logout by the link in the header', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signOutFOByHeaderLink', baseContext);

    await foClassicHomePage.logout(page);

    const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
  });

  it('should sign in with default customer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFO2', baseContext);

    await foClassicHomePage.goToLoginPage(page);
    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);
  });

  it('should go to my account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicMyAccountPage.pageTitle);
  });

  it('should logout by the link in the footer of account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signOutFOByFooterLink', baseContext);

    await foClassicMyAccountPage.logout(page);

    const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
  });
});
