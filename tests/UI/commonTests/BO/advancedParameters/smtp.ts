// Import utils
import testContext from '@utils/testContext';

import {
  boDashboardPage,
  boEmailPage,
  boLoginPage,
  type BrowserContext,
  dataCustomers,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

import {expect} from 'chai';

let browserContext: BrowserContext;
let page: Page;

const {smtpServer, smtpPort} = global.maildevConfig;

/**
 * Setup SMTP configuration
 * @param baseContext {string} String to identify the test
 */
function setupSmtpConfigTest(baseContext: string = 'commonTests-configSMTP'): void {
  describe('PRE-TEST: Setup SMTP config', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailSetupPageForSetupSmtpParams', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.emailLink,
      );
      await boEmailPage.closeSfToolBar(page);

      const pageTitle = await boEmailPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmailPage.pageTitle);
    });

    it('should fill the smtp parameters form fields', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillSmtpParametersFormField', baseContext);

      const alertSuccessMessage = await boEmailPage.setupSmtpParameters(
        page,
        smtpServer,
        dataCustomers.johnDoe.email,
        dataCustomers.johnDoe.password,
        smtpPort.toString(),
      );
      expect(alertSuccessMessage).to.contains(boEmailPage.successfulUpdateMessage);
    });
  });
}

/**
 * Reset SMTP configuration
 * @param baseContext {string} String to identify the test
 */
function resetSmtpConfigTest(baseContext: string = 'commonTests-configSMTP'): void {
  describe('POST-TEST: Reset SMTP config', async () => {
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailSetupPageForResetSmtpParams', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.advancedParametersLink,
        boDashboardPage.emailLink,
      );
      await boEmailPage.closeSfToolBar(page);

      const pageTitle = await boEmailPage.getPageTitle(page);
      expect(pageTitle).to.contains(boEmailPage.pageTitle);
    });

    it('should reset parameters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetMailParameters', baseContext);

      const successParametersReset = await boEmailPage.resetDefaultParameters(page);
      expect(successParametersReset).to.contains(boEmailPage.successfulUpdateMessage);
    });
  });
}

export {setupSmtpConfigTest, resetSmtpConfigTest};
