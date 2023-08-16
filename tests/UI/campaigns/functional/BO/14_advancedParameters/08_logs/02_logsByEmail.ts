// Import utils
import helper from '@utils/helpers';
import {expect} from 'chai';
import testContext from '@utils/testContext';

// Import common
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import logsPage from '@pages/BO/advancedParameters/logs';

// Import data
import Employees from '@data/demo/employees';

import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_logs_logsByEmail';

describe('BO - Advanced Parameters - Logs : Logs by email', async () => {
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

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Advanced Parameters > Logs\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLogsPageToEraseLogs', baseContext);

    await dashboardPage.goToSubMenu(page, dashboardPage.advancedParametersLink, dashboardPage.logsLink);
    await logsPage.closeSfToolBar(page);

    const pageTitle = await logsPage.getPageTitle(page);
    expect(pageTitle).to.contains(logsPage.pageTitle);
  });

  it('should enter an invalid email in \'Send emails to\' input and check the error message', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setInvalidEmail', baseContext);

    const errorMessage = await logsPage.setEmail(page, 'demo@prestashop.');
    expect(errorMessage).to.eq('Invalid email: demo@prestashop..');
  });

  it('should enter a valid email in \'Send emails to\' input', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'setValidEmail', baseContext);

    const errorMessage = await logsPage.setEmail(page, Employees.DefaultEmployee.email);
    expect(errorMessage).to.eq(logsPage.successfulUpdateMessage);
  });
});
