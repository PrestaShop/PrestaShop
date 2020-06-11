require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import test context
const testContext = require('@utils/testContext');

// Importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const EmailPage = require('@pages/BO/advancedParameters/email');


const baseContext = 'functional_BO_advancedParameters_email_sendTestEmail';

let browserContext;
let page;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    emailPage: new EmailPage(page),
  };
};

/*
Send test email and check successful message
 */
describe('Send test email', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO
  loginCommon.loginBO();

  it('should go to \'Advanced parameters > E-mail\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.advancedParametersLink,
      this.pageObjects.dashboardPage.emailLink,
    );

    const pageTitle = await this.pageObjects.emailPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.emailPage.pageTitle);
  });

  it('should check successful message after sending test email', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sendTestEmail', baseContext);

    const textResult = await this.pageObjects.emailPage.sendTestEmail(global.BO.EMAIL);
    await expect(textResult).to.contains(this.pageObjects.emailPage.sendTestEmailSuccessfulMessage);
  });
});
