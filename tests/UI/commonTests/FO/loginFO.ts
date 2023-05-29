// Import test context
import testContext from '@utils/testContext';

// Import FO pages
import {loginPage} from '@pages/FO/login';
import {myAccountPage} from '@pages/FO/myAccount';

// Import data
import type CustomerData from '@data/faker/customer';

import {expect} from 'chai';
import type {Context} from 'mocha';
import type {Page} from 'playwright';

export default {
  async loginFO(mochaContext: Context, page: Page, customer: CustomerData): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginFO');

    await loginPage.goTo(page, global.FO.URL);
    await myAccountPage.goToLoginPage(page);
    await loginPage.customerLogin(page, customer);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is not connected').to.be.true;

    const pageTitle = await myAccountPage.getPageTitle(page);
    await expect(pageTitle).to.contains(myAccountPage.pageTitle);
  },

  async logoutFO(mochaContext: Context, page: Page): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutFO');

    await loginPage.goToMyAccountPage(page);
    await loginPage.logout(page);

    const isCustomerConnected = await myAccountPage.isCustomerConnected(page);
    await expect(isCustomerConnected, 'Customer is still connected').to.be.false;
  },
};
