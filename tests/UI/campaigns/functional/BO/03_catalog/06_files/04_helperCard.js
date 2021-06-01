require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const filesPage = require('@pages/BO/catalog/files');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_files_helperCard';


let browserContext;
let page;

describe('Helper card on files page', async () => {
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

  it('should go to files page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFilesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.filesLink,
    );

    await filesPage.closeSfToolBar(page);

    const pageTitle = await filesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(filesPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await filesPage.openHelpSideBar(page);
    await expect(isHelpSidebarVisible).to.be.true;

    const documentURL = await filesPage.getHelpDocumentURL(page);
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await filesPage.closeHelpSideBar(page);
    await expect(isHelpSidebarClosed).to.be.true;
  });
});
