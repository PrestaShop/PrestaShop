// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import {homePage} from '@pages/FO/classic/home';
import {loginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';

import {
  // Import data
  dataCustomers,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_FO_classic_userAccount_logOut';

describe('FO - User Account : LogOut', async () => {
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

  it('should open the shop page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToShopFO', baseContext);

    await homePage.goTo(page, global.FO.URL);

    const result = await homePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should logIn', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'enterValidCredentials', baseContext);

    await homePage.goToLoginPage(page);
    await loginPage.customerLogin(page, dataCustomers.johnDoe);

    const isCustomerConnected = await loginPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected!').to.eq(true);

    const result = await homePage.isHomePage(page);
    expect(result).to.eq(true);
  });

  it('should go to my account page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

    await homePage.goToMyAccountPage(page);

    const pageTitle = await myAccountPage.getPageTitle(page);
    expect(pageTitle).to.equal(myAccountPage.pageTitle);
  });

  it('should logOut with link in the footer', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signOutWithLinkAtAccountPage', baseContext);

    await myAccountPage.logout(page);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is connected!').to.eq(false);
  });
});
