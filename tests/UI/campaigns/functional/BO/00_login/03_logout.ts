// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {boLoginPage} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_login_logout';

/*
Pre-condition
- Login to BO
Scenario:
- Logout from BO
 */

describe('BO - logout : log out from BO', async () => {
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

  it('should logout from BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'logoutFromBOPage', baseContext);

    await loginCommon.logoutBO(this, page);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);
  });
});
