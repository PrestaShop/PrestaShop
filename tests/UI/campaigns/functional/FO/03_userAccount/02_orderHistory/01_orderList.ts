// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import FO pages
import foHomePage from '@pages/FO/home';
import foLoginPage from '@pages/FO/login';
import foOrderHistoryPage from '@pages/FO/myAccount/orderHistory';
import foMyAccountPage from '@pages/FO/myAccount';

// Import data
import {DefaultCustomer} from '@data/demo/customer';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext = 'functional_FO_userAccount_orderHistory_orderList';

/*
Sign in FO with default account
Go to orders history page
Check that number of orders is above 5 (default orders)
 */

describe('FO - Account : Check number of orders in order history page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const numberOfDefaultOrders: number = 5;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });
  it('should go to FO home page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFoToCreateAccount', baseContext);

    await foHomePage.goToFo(page);

    const isHomePage = await foHomePage.isHomePage(page);
    await expect(isHomePage).to.be.true;
  });

  it('should go to login page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLoginFoPage', baseContext);

    await foHomePage.goToLoginPage(page);

    const pageHeaderTitle = await foLoginPage.getPageTitle(page);
    await expect(pageHeaderTitle).to.equal(foLoginPage.pageTitle);
  });

  it('Should sign in FO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'signInFo', baseContext);

    await foLoginPage.customerLogin(page, DefaultCustomer);

    const isCustomerConnected = await foMyAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;
  });

  it('should go to order history page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToOrderHistoryPage', baseContext);

    await foHomePage.goToMyAccountPage(page);
    await foMyAccountPage.goToHistoryAndDetailsPage(page);

    const pageHeaderTitle = await foOrderHistoryPage.getPageTitle(page);
    await expect(pageHeaderTitle).to.equal(foOrderHistoryPage.pageTitle);
  });

  it('should check number of orders', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkOrderList', baseContext);

    const numberOfOrders = await foOrderHistoryPage.getNumberOfOrders(page);
    await expect(numberOfOrders).to.be.at.least(numberOfDefaultOrders);
  });
});
