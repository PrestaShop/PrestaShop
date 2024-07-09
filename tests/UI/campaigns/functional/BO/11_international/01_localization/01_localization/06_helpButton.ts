// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boLocalizationPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_international_localization_localization_helpButton';

// Check that help button is in english in localization page
describe('BO - International - Localization : Help Button', async () => {
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

  it('should go to \'International > Localization\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToLocalizationPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.internationalParentLink,
      boDashboardPage.localizationLink,
    );

    const pageTitle = await boLocalizationPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLocalizationPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await boLocalizationPage.openHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);

    const documentURL = await boLocalizationPage.getHelpDocumentURL(page);
    expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarVisible = await boLocalizationPage.closeHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);
  });
});
