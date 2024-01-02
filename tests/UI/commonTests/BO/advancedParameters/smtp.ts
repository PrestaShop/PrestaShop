// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import emailPage from '@pages/BO/advancedParameters/email';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import Customers from '@data/demo/customers';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailSetupPageForSetupSmtpParams', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.emailLink,
      );
      await emailPage.closeSfToolBar(page);

      const pageTitle = await emailPage.getPageTitle(page);
      expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should fill the smtp parameters form fields', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillSmtpParametersFormField', baseContext);

      const alertSuccessMessage = await emailPage.setupSmtpParameters(
        page,
        smtpServer,
        Customers.johnDoe.email,
        Customers.johnDoe.password,
        smtpPort.toString(),
      );
      expect(alertSuccessMessage).to.contains(emailPage.successfulUpdateMessage);
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
      await testContext.addContextItem(this, 'testIdentifier', 'goToEmailSetupPageForResetSmtpParams', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.advancedParametersLink,
        dashboardPage.emailLink,
      );
      await emailPage.closeSfToolBar(page);

      const pageTitle = await emailPage.getPageTitle(page);
      expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should reset parameters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetMailParameters', baseContext);

      const successParametersReset = await emailPage.resetDefaultParameters(page);
      expect(successParametersReset).to.contains(emailPage.successfulUpdateMessage);
    });
  });
}

export {setupSmtpConfigTest, resetSmtpConfigTest};
