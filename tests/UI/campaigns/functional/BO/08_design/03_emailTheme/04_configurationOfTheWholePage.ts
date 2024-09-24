// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import emailThemesPage from '@pages/BO/design/emailThemes';
import previewEmailThemesPage from '@pages/BO/design/emailThemes/preview';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
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
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Design > Email Theme\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmailThemePage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.emailThemeLink,
    );
    await emailThemesPage.closeSfToolBar(page);

    const pageTitle = await emailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(emailThemesPage.pageTitle);
  });

  describe('Preview email theme', async () => {
    it('should preview email theme \'classic\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'previewEmailThemeClassic', baseContext);

      await emailThemesPage.previewEmailTheme(page, 'classic');

      const pageTitle = await previewEmailThemesPage.getPageTitle(page);
      expect(pageTitle).to.contains(`${previewEmailThemesPage.pageTitle} 'classic'`);
    });
  });
});
