// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import emailPage from '@pages/BO/advancedParameters/email';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_email_sendTestEmail_sendTestEmail';

/*
Send test email and check successful message
 */
describe('BO - Advanced Parameters - Email : Send test email', async () => {
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

  it('should go to \'Advanced Parameters > E-mail\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.emailLink,
    );

    const pageTitle = await emailPage.getPageTitle(page);
    expect(pageTitle).to.contains(emailPage.pageTitle);
  });

  it('should check successful message after sending test email', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sendTestEmail', baseContext);

    const textResult = await emailPage.sendTestEmail(page, global.BO.EMAIL);
    expect(textResult).to.contains(emailPage.sendTestEmailSuccessfulMessage);
  });
});
