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
const previewEmailThemesPage = require('@pages/BO/design/emailThemes/preview');

const baseContext = 'functional_BO_design_emailThemes_previewEmailTheme';

let browserContext;
let page;

describe('BO - Design - Email Theme : Preview email theme', async () => {
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

  describe('Preview email theme', async () => {
    [
      {args: {emailThemeName: 'classic', numberOfLayouts: 50}},
      {args: {emailThemeName: 'modern', numberOfLayouts: 50}},
    ].forEach((test) => {
      it(`should preview email theme '${test.args.emailThemeName}'`, async function () {
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
      });

      it('should check number of layouts', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkNumberLayouts_${test.args.emailThemeName}`,
          baseContext,
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
