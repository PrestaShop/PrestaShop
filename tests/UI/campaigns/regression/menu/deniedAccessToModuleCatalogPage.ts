import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boErrorPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'regression_menu_deniedAccessToModuleCatalogPage';

describe('Regression : Access to Module catalog is denied with neither left menu and Url', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const pageLegacyUrl: string = `${global.BO.URL}index.php?controller=AdminModulesCatalog`;
  const pageSymfonyUrl: string = `${global.BO.URL}index.php/modules/addons/modules/catalog`;

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

  it('should go check that `Module Catalog` on left menu is not visible', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'menuTabNotVisible', baseContext);

    const isMenuTabVisible = await boDashboardPage.isSubmenuVisible(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleCatalogueLink,
    );
    expect(isMenuTabVisible, 'The Menu tab is still visible').to.eq(false);
  });

  it('should trigger a not found alert when accessing by legacy url', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkUrlAccessibility', baseContext);

    await boDashboardPage.navigateToPageWithInvalidToken(page, pageLegacyUrl);

    const alertText = await boErrorPage.getAlertDangerBlockParagraphContent(page);
    expect(alertText).to.contain(boErrorPage.pageNotFoundMessage);
  });

  it('should redirect to dashboard when accessing by symfony url', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkUrlAccessibility2', baseContext);

    await boDashboardPage.navigateToPageWithInvalidToken(page, pageSymfonyUrl);

    const pageTitle = await boErrorPage.getPageTitle(page);
    expect(pageTitle).to.contains(boErrorPage.notFoundTitle);
  });
});
