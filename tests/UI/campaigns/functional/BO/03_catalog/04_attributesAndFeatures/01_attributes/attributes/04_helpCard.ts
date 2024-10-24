// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boAttributesPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_attributes_attributes_helpCard';

/*
Go to attributes page
Open help card and check language
Close help card
 */
describe('BO - Catalog - Attributes & Features : Help card on attributes page', async () => {
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

  it('should go to \'Catalog > Attributes & Features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.attributesAndFeaturesLink,
    );
    await boAttributesPage.closeSfToolBar(page);

    const pageTitle = await boAttributesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boAttributesPage.pageTitle);
  });

  it('should open the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await boAttributesPage.openHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);
  });

  it('should check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentLanguage', baseContext);

    const documentURL = await boAttributesPage.getHelpDocumentURL(page);
    expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await boAttributesPage.closeHelpSideBar(page);
    expect(isHelpSidebarClosed).to.eq(true);
  });
});
