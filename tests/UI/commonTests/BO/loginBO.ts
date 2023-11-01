import testContext from '@utils/testContext';

import loginPage from '@pages/BO/login';
import dashboardPage from '@pages/BO/dashboard';

import {expect} from 'chai';
import {Context} from 'mocha';
import type {Page} from 'playwright';

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

    const pageTitle = await dashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(dashboardPage.pageTitle);
  },

  async logoutBO(mochaContext: Context, page: Page): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutBO');

    await dashboardPage.logoutBO(page);

    const pageTitle = await loginPage.getPageTitle(page);
    expect(pageTitle).to.contains(loginPage.pageTitle);
  },
};
