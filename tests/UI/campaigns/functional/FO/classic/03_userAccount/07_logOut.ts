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

const baseContext: string = 'functional_FO_classic_userAccount_logOut';

describe('FO - User Account : LogOut', async () => {
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

  it('should logIn', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

    await foClassicHomePage.goToLoginPage(page);
    await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await foClassicLoginPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);

    const result = await foClassicHomePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should go to my account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

    await foClassicHomePage.goToMyAccountPage(page);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).to.equal(foClassicMyAccountPage.pageTitle);
  });

  it('should logOut with link in the footer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signOutWithLinkAtAccountPage', baseContext);

    await foClassicMyAccountPage.logout(page);

    const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
  });
});
