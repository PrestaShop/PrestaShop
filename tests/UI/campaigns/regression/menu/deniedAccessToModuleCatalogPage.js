require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Importing pages
const dashboardPage = require('@pages/BO/dashboard');
const moduleCatalogPage = require('@pages/BO/modules/moduleCatalog');

// Setup data
const pageLegacyUrl = `${global.BO.URL}index.php?controller=AdminModulesCatalog`;
const pageSymfonyUrl = `${global.BO.URL}index.php/modules/addons/modules/catalog`;

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'regression_menu_deniedAccessToModuleCatalogPage';

let browserContext;
let page;

describe('Regression : Access to Module catalog is denied with neither left menu and Url', async () => {
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

    await expect(isMenuTabVisible, 'The Menu tab is still visible').to.be.false;
  });

  it('should trigger a not found alert when accessing by legacy url', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkUrlAccessibility', baseContext);

    await dashboardPage.navigateToPageWithInvalidToken(page, pageLegacyUrl);

    const alertText = await moduleCatalogPage.getAlertDangerBlockParagraphContent(page);
    await expect(alertText).to.contain(moduleCatalogPage.pageNotFoundMessage);
  });

  it('should redirect to dashboard when accessing by symfony url', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkUrlAccessibility2', baseContext);

    await dashboardPage.navigateToPageWithInvalidToken(page, pageSymfonyUrl);

    const pageTitle = await dashboardPage.getPageTitle(page);
    await expect(pageTitle).to.contains(dashboardPage.pageTitle);
  });
});
