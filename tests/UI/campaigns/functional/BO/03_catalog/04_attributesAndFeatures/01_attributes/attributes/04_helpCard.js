// Import utils
import helper from '@utils/helpers';

// Import test context
import testContext from '@utils/testContext';

// Common tests login BO
import loginCommon from '@commonTests/BO/loginBO';

require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const attributesPage = require('@pages/BO/catalog/attributes');

const baseContext = 'functional_BO_catalog_attributesAndFeatures_attributes_attributes_helpCard';

let browserContext;
let page;

/*
Go to attributes page
Open help card and check language
Close help card
 */
describe('BO - Catalog - Attributes & Features : Help card on attributes page', async () => {
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

  it('should go to \'Catalog > Attributes & Features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.attributesAndFeaturesLink,
    );

    await attributesPage.closeSfToolBar(page);

    const pageTitle = await attributesPage.getPageTitle(page);
    await expect(pageTitle).to.contains(attributesPage.pageTitle);
  });

  it('should open the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await attributesPage.openHelpSideBar(page);
    await expect(isHelpSidebarVisible).to.be.true;
  });

  it('should check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentLanguage', baseContext);

    const documentURL = await attributesPage.getHelpDocumentURL(page);
    await expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await attributesPage.closeHelpSideBar(page);
    await expect(isHelpSidebarClosed).to.be.true;
  });
});
