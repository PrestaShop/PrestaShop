import testContext from '@utils/testContext';

import loginPage from '@pages/BO/login';

import {expect} from 'chai';
import {Context} from 'mocha';
import type {Page} from 'playwright';
import {boDashboardPage} from '@prestashop-core/ui-testing';

export default {
  async loginBO(
    mochaContext: Context,
    page: Page,
    email: string = global.BO.EMAIL,
    password: string = global.BO.PASSWD,
  ): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginBO');

    await loginPage.goTo(page, global.BO.URL);
    await loginPage.successLogin(page, email, password);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  },

  async logoutBO(mochaContext: Context, page: Page): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutBO');

    await boDashboardPage.logoutBO(page);

    const pageTitle = await loginPage.getPageTitle(page);
    expect(pageTitle).to.contains(loginPage.pageTitle);
  },
};
