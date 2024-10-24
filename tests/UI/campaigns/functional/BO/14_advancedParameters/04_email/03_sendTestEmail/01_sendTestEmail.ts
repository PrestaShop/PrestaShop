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

const baseContext: string = 'functional_BO_advancedParameters_email_sendTestEmail_sendTestEmail';

/*
Send test email and check successful message
 */
describe('BO - Advanced Parameters - Email : Send test email', async () => {
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

  it('should check successful message after sending test email', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sendTestEmail', baseContext);

    const textResult = await boEmailPage.sendTestEmail(page, global.BO.EMAIL);
    expect(textResult).to.contains(boEmailPage.sendTestEmailSuccessfulMessage);
  });
});
