// Import test context
import testContext from '@utils/testContext';

// Import FO pages
import {loginPage} from '@pages/FO/classic/login';
import {myAccountPage} from '@pages/FO/classic/myAccount';

import {
  // Import data
  FakerCustomer,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {Context} from 'mocha';
import type {Page} from 'playwright';

export default {
  async loginFO(mochaContext: Context, page: Page, customer: FakerCustomer): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginFO');

    await loginPage.goTo(page, global.FO.URL);
    await myAccountPage.goToLoginPage(page);
    await loginPage.customerLogin(page, customer);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected').to.eq(true);

    const pageTitle = await myAccountPage.getPageTitle(page);
    expect(pageTitle).to.contains(myAccountPage.pageTitle);
  },

  async logoutFO(mochaContext: Context, page: Page): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutFO');

    await loginPage.goToMyAccountPage(page);
    await loginPage.logout(page);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is still connected').to.eq(false);
  },
};
