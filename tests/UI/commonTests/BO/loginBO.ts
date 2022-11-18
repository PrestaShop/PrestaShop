import testContext from '@utils/testContext';
import {expect} from 'chai';
import loginPage from '@pages/BO/login';
import dashboardPage from '@pages/BO/dashboard';
import {Page} from 'playwright';

require('module-alias/register');

export default {
  async loginBO(mochaContext: Mocha.Context, page: Page, email = global.BO.EMAIL, password = global.BO.PASSWD): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'loginBO');
    await loginPage.goTo(page, global.BO.URL);
    await loginPage.successLogin(page, email, password);
    const pageTitle = await dashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(dashboardPage.pageTitle);
  },

  async logoutBO(mochaContext: Mocha.Context, page: Page): Promise<void> {
    await testContext.addContextItem(mochaContext, 'testIdentifier', 'logoutBO');
    await dashboardPage.logoutBO(page);
    const pageTitle = await loginPage.getPageTitle(page);
    expect(pageTitle).to.contains(loginPage.pageTitle);
  },
};
