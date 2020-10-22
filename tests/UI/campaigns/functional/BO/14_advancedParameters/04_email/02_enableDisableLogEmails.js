require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const emailPage = require('@pages/BO/advancedParameters/email');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_advancedParameters_email_enableDisableLogEmails';

let browserContext;
let page;

describe('Enable/Disable log emails', async () => {
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

  it('should go to \'Advanced parameters > E-mail\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.emailLink,
    );

    const pageTitle = await emailPage.getPageTitle(page);
    await expect(pageTitle).to.contains(emailPage.pageTitle);
  });

  const tests = [
    {args: {action: 'disable', exist: false}},
    {args: {action: 'enable', exist: true}},
  ];

  tests.forEach((test, index) => {
    it(`should ${test.args.action} log emails`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}LogEmails`, baseContext);

      const result = await emailPage.setLogEmails(page, test.args.exist);
      await expect(result).to.contains(emailPage.successfulUpdateMessage);
    });

    it('should check the existence of log emails table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `checkLogEmailsTable${index}`, baseContext);

      const isVisible = await emailPage.isLogEmailsTableVisible(page);
      await expect(isVisible).to.equal(test.args.exist);
    });
  });
});
