require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import test context
const testContext = require('@utils/testContext');

// Importing pages
const dashboardPage = require('@pages/BO/dashboard');
const emailPage = require('@pages/BO/advancedParameters/email');


const baseContext = 'functional_BO_advancedParameters_email_sendTestEmail';

let browserContext;
let page;

/*
Send test email and check successful message
 */
describe('Send test email', async () => {
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

  it('should check successful message after sending test email', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'sendTestEmail', baseContext);

    const textResult = await emailPage.sendTestEmail(page, global.BO.EMAIL);
    await expect(textResult).to.contains(emailPage.sendTestEmailSuccessfulMessage);
  });
});
