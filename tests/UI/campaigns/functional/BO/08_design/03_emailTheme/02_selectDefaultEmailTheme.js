require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Import login steps
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const emailThemesPage = require('@pages/BO/design/emailThemes');

const baseContext = 'functional_BO_design_emailThemes_selectDefaultEmailTheme';

let browserContext;
let page;

describe('BO - Design - Email Theme : Select default email theme', async () => {
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

  it('should go to \'Design > Email Theme\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemePage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.emailThemeLink,
    );

    await emailThemesPage.closeSfToolBar(page);

    const pageTitle = await emailThemesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(emailThemesPage.pageTitle);
  });

  ['classic', 'modern'].forEach((emailTheme) => {
    it(`should select '${emailTheme}' as default email theme`, async function () {
      await testContext.addContextItem(
        this,
        'testIdentifier',
        `${emailTheme}AsDefaultEmailTheme`,
        baseContext,
      );

      const textMessage = await emailThemesPage.selectDefaultEmailTheme(page, emailTheme);
      await expect(textMessage).to.contains(emailThemesPage.emailThemeConfigurationSuccessfulMessage);
    });
  });
});
