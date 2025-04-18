// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boDesignEmailThemesPage,
  boDesignEmailThemesPreviewPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_emailTheme_previewEmailThemes';

describe('BO - Design - Email Theme : Preview email theme', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Design > Email Theme\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.emailThemeLink,
    );
    await boDesignEmailThemesPage.closeSfToolBar(page);

    const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
  });

  describe('Preview email theme', async () => {
    [
      {args: {emailThemeName: 'classic', numberOfLayouts: 43}},
      {args: {emailThemeName: 'modern', numberOfLayouts: 43}},
    ].forEach((test) => {
      it(`should preview email theme '${test.args.emailThemeName}'`, async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `previewEmailTheme_${test.args.emailThemeName}`,
          baseContext,
        );

        await boDesignEmailThemesPage.previewEmailTheme(page, test.args.emailThemeName);

        const pageTitle = await boDesignEmailThemesPreviewPage.getPageTitle(page);
        expect(pageTitle).to.contains(
          `${boDesignEmailThemesPreviewPage.pageTitle} ${test.args.emailThemeName}`,
        );
      });

      it('should check number of layouts', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `checkNumberLayouts_${test.args.emailThemeName}`,
          baseContext,
        );

        const numberOfLayouts = await boDesignEmailThemesPreviewPage.getNumberOfLayoutInGrid(page);
        expect(numberOfLayouts).to.equal(test.args.numberOfLayouts);
      });

      it('should go back to email themes page', async function () {
        await testContext.addContextItem(
          this,
          'testIdentifier',
          `backToEmailThemePageFrom${test.args.emailThemeName}`,
          baseContext,
        );

        await boDesignEmailThemesPreviewPage.goBackToEmailThemesPage(page);

        const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
        expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
      });
    });
  });
});
