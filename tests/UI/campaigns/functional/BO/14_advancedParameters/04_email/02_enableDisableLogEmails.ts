// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boEmailPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Advanced Parameters > E-mail\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.emailLink,
    );

    const pageTitle = await boEmailPage.getPageTitle(page);
    expect(pageTitle).to.contains(boEmailPage.pageTitle);
  });

  const tests = [
    {args: {action: 'disable', exist: false}},
    {args: {action: 'enable', exist: true}},
  ];

  tests.forEach((test, index: number) => {
    it(`should ${test.args.action} log emails`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}LogEmails`, baseContext);

      const result = await boEmailPage.setLogEmails(page, test.args.exist);
      expect(result).to.contains(boEmailPage.successfulUpdateMessage);
    });

    it('should check the existence of E-mail table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkEmailTable${index}`, baseContext);

      const isVisible = await boEmailPage.isLogEmailsTableVisible(page);
      expect(isVisible).to.equal(test.args.exist);
    });
  });
});
