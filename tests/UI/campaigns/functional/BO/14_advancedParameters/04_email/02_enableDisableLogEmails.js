require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const EmailPage = require('@pages/BO/advancedParameters/email');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_email_enableDisableLogEmails';


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

describe('Enable/Disable log emails', async () => {
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

  const tests = [
    {args: {action: 'disable', exist: false}},
    {args: {action: 'enable', exist: true}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} log emails`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}LogEmails`, baseContext);

      const result = await this.pageObjects.emailPage.setLogEmails(test.args.exist);
      await expect(result).to.contains(this.pageObjects.emailPage.successfulUpdateMessage);
    });

    it('should check the existence of log emails table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkLogEmailsTable${index}`, baseContext);

      const isVisible = await this.pageObjects.emailPage.isLogEmailsTableVisible();
      await expect(isVisible).to.equal(test.args.exist);
    });
  });
});
