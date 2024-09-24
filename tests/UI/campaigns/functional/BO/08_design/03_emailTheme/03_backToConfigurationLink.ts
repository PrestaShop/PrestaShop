// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import previewEmailThemesPage from '@pages/BO/design/emailThemes/preview';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boDesignEmailThemesPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_emailTheme_backToConfigurationLink';

describe('BO - Design - Email Theme : Back to configuration link', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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
    await boDesignEmailThemesPage.closeSfToolBar(page);

    const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
  });

  it('should preview email theme \'classic\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'previewEmailTheme', baseContext);

    await boDesignEmailThemesPage.previewEmailTheme(page, 'classic');

    const pageTitle = await previewEmailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(`${previewEmailThemesPage.pageTitle} classic`);
  });

  it('should go back to email themes page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'backToEmailThemePage', baseContext);

    await previewEmailThemesPage.goBackToEmailThemesPage(page);

    const pageTitle = await boDesignEmailThemesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDesignEmailThemesPage.pageTitle);
  });
});
