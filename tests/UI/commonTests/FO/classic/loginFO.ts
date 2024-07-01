// Import test context
import testContext from '@utils/testContext';

// Import FO pages
import {myAccountPage} from '@pages/FO/classic/myAccount';

import {
  FakerCustomer,
  foClassicLoginPage,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {Context} from 'mocha';
import type {Page} from 'playwright';

export default {
  async loginFO(mochaContext: Context, page: Page, customer: FakerCustomer): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginFO');

    await foClassicLoginPage.goTo(page, global.FO.URL);
    await myAccountPage.goToLoginPage(page);
    await foClassicLoginPage.customerLogin(page, customer);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected').to.eq(true);

    const pageTitle = await myAccountPage.getPageTitle(page);
    expect(pageTitle).to.contains(myAccountPage.pageTitle);
  },

  async logoutFO(mochaContext: Context, page: Page): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutFO');

    await foClassicLoginPage.goToMyAccountPage(page);
    await foClassicLoginPage.logout(page);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is still connected').to.eq(false);
  },
};
