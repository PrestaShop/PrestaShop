// Import utils
import helper from '@utils/helpers';

// Import test context
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

require('module-alias/register');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const emailPage = require('@pages/BO/advancedParameters/email');

// Import data
const {DefaultCustomer} = require('@data/demo/customer');

// Import expect from chai
const {expect} = require('chai');

let browserContext;
let page;

// Maildev config
const {smtpServer, smtpPort} = global.maildevConfig;

/**
 * Setup SMTP configuration
 * @param baseContext {string} String to identify the test
 */
function setupSmtpConfigTest(baseContext = 'commonTests-configSMTP') {
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
      await expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should fill the smtp parameters form fields', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'fillSmtpParametersFormField', baseContext);

      const alertSuccessMessage = await emailPage.setupSmtpParameters(
        page,
        smtpServer,
        DefaultCustomer.email,
        DefaultCustomer.password,
        smtpPort,
      );

      await expect(alertSuccessMessage).to.contains(emailPage.successfulUpdateMessage);
    });
  });
}

/**
 * Reset SMTP configuration
 * @param baseContext {string} String to identify the test
 */
function resetSmtpConfigTest(baseContext = 'commonTests-configSMTP') {
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
      await expect(pageTitle).to.contains(emailPage.pageTitle);
    });

    it('should reset parameters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetMailParameters', baseContext);

      const successParametersReset = await emailPage.resetDefaultParameters(page);
      await expect(successParametersReset).to.contains(emailPage.successfulUpdateMessage);
    });
  });
}

module.exports = {setupSmtpConfigTest, resetSmtpConfigTest};
