require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const emailThemesPage = require('@pages/BO/design/emailThemes');
const previewEmailThemesPage = require('@pages/BO/design/emailThemes/preview');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_emailThemes_previewEmailThemes';


let browserContext;
let page;

describe('Preview Email themes classic and modern', async () => {
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

  it('should go to design > email themes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.emailThemeLink,
    );

    await emailThemesPage.closeSfToolBar(page);

    const pageTitle = await emailThemesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(emailThemesPage.pageTitle);
  });

  describe('Preview email themes', async () => {
    const tests = [
      {args: {emailThemeName: 'classic', numberOfLayouts: 50}},
      {args: {emailThemeName: 'modern', numberOfLayouts: 50}},
    ];

    tests.forEach((test) => {
      it(`should preview email theme ${test.args.emailThemeName} and check number of layouts`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `previewEmailTheme_${test.args.emailThemeName}`,
          baseContext,
        );

        await emailThemesPage.previewEmailTheme(page, test.args.emailThemeName);

        const pageTitle = await emailThemesPage.getPageTitle(page);

        await expect(pageTitle).to.contains(
          `${previewEmailThemesPage.pageTitle} ${test.args.emailThemeName}`,
        );

        const numberOfLayouts = await previewEmailThemesPage.getNumberOfLayoutInGrid(page);
        await expect(numberOfLayouts).to.equal(test.args.numberOfLayouts);
      });

      it('should go back to email themes page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `backToEmailThemePageFrom${test.args.emailThemeName}`,
          baseContext,
        );

        await previewEmailThemesPage.goBackToEmailThemesPage(page);
        const pageTitle = await emailThemesPage.getPageTitle(page);
        await expect(pageTitle).to.contains(emailThemesPage.pageTitle);
      });
    });
  });
});
