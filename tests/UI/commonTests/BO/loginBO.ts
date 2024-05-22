import testContext from '@utils/testContext';

import {expect} from 'chai';
import {Context} from 'mocha';
import type {Page} from 'playwright';
import {
  boDashboardPage,
  boLoginPage,
} from '@prestashop-core/ui-testing';

export default {
  async loginBO(
    mochaContext: Context,
    page: Page,
    email: string = global.BO.EMAIL,
    password: string = global.BO.PASSWD,
  ): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginBO');

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, email, password);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  },

  async logoutBO(mochaContext: Context, page: Page): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutBO');

    await boDashboardPage.logoutBO(page);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  },
};
