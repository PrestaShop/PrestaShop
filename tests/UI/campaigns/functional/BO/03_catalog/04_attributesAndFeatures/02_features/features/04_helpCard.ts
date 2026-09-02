import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boAttributesPage,
  boDashboardPage,
  boFeaturesPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_features_features_helpCard';

/*
Go to features page
Open helper card and check language
Close helper card
 */
describe('BO - Catalog - Attributes & Features : Help card on features page', async () => {
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

  it('should go to Features page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToFeaturesPage', baseContext);

    await boAttributesPage.goToFeaturesPage(page);

    const pageTitle = await boFeaturesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boFeaturesPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await boFeaturesPage.openHelpSideBar(page);
    expect(isHelpSidebarVisible, 'Help side bar is not opened!').to.eq(true);
  });

  it('should check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkDocumentLanguage', baseContext);

    const documentURL = await boFeaturesPage.getHelpDocumentURL(page);
    expect(documentURL, 'Help document is not in english language!').to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await boFeaturesPage.closeHelpSideBar(page);
    expect(isHelpSidebarClosed, 'Help document is not closed!').to.eq(true);
  });
});
