// Import test context
import testContext from '@utils/testContext';

import {
  FakerCustomer,
  foClassicLoginPage,
  foClassicMyAccountPage,
  type Page,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';
import type {Context} from 'mocha';

export default {
  async loginFO(mochaContext: Context, page: Page, customer: FakerCustomer): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginFO');

    await foClassicLoginPage.goTo(page, global.FO.URL);
    await foClassicMyAccountPage.goToLoginPage(page);
    await foClassicLoginPage.customerLogin(page, customer);

    const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is not connected').to.eq(true);

    const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
    expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
  },

  async logoutFO(mochaContext: Context, page: Page): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutFO');

    await foClassicLoginPage.goToMyAccountPage(page);
    await foClassicLoginPage.logout(page);

    const isCustomerConnected = await foClassicMyAccountPage.isCustomerConnected(page);
    expect(isCustomerConnected, 'Customer is still connected').to.eq(false);
  },
};
