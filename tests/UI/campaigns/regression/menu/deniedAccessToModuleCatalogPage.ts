// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import moduleCatalogPage from '@pages/BO/modules/moduleCatalog';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'regression_menu_deniedAccessToModuleCatalogPage';

describe('Regression : Access to Module catalog is denied with neither left menu and Url', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pageLegacyUrl: string = `${global.BO.URL}index.php?controller=AdminModulesCatalog`;
  const pageSymfonyUrl: string = `${global.BO.URL}index.php/modules/addons/modules/catalog`;

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

  it('should go check that `Module Catalog` on left menu is not visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'menuTabNotVisible', baseContext);

    const isMenuTabVisible = await dashboardPage.isSubmenuVisible(
      page,
      dashboardPage.modulesParentLink,
      dashboardPage.moduleCatalogueLink,
    );
    expect(isMenuTabVisible, 'The Menu tab is still visible').to.eq(false);
  });

  it('should trigger a not found alert when accessing by legacy url', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkUrlAccessibility', baseContext);

    await dashboardPage.navigateToPageWithInvalidToken(page, pageLegacyUrl);

    const alertText = await moduleCatalogPage.getAlertDangerBlockParagraphContent(page);
    expect(alertText).to.contain(moduleCatalogPage.pageNotFoundMessage);
  });

  it('should redirect to dashboard when accessing by symfony url', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkUrlAccessibility2', baseContext);

    await dashboardPage.navigateToPageWithInvalidToken(page, pageSymfonyUrl);

    const pageTitle = await dashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(dashboardPage.pageTitle);
  });
});
