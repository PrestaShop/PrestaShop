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

const baseContext: string = 'functional_BO_advancedParameters_email_enableDisableLogEmails';

/*
Enable/Disable log emails
Check the existence of E-mail table
 */
describe('BO - Advanced Parameters - E-mail : Enable/Disable log emails', async () => {
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

  const tests = [
    {args: {action: 'disable', exist: false}},
    {args: {action: 'enable', exist: true}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} log emails`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}LogEmails`, baseContext);

      const result = await emailPage.setLogEmails(page, test.args.exist);
      expect(result).to.contains(emailPage.successfulUpdateMessage);
    });

    it('should check the existence of E-mail table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkEmailTable${index}`, baseContext);

      const isVisible = await emailPage.isLogEmailsTableVisible(page);
      expect(isVisible).to.equal(test.args.exist);
    });
  });
});
