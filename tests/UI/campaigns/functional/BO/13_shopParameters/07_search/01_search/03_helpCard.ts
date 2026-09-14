import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boSearchPage,
  boSearchAliasPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shopParameters_search_search_helpCard';

describe('BO - Shop Parameters - Search : Help card on Aliases page', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to the Aliases page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAliasesPage', baseContext);

    const searchPageURL = await boDashboardPage.getAttributeContent(
      page,
      `${boDashboardPage.searchLink} a`,
      'href',
    );
    await boSearchPage.goTo(page, searchPageURL);
    await boSearchPage.goToAliasesPage(page);

    const pageTitle = await boSearchAliasPage.getPageTitle(page);
    expect(pageTitle).to.equals(boSearchAliasPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await boSearchAliasPage.openHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);

    const documentURL = await boSearchAliasPage.getHelpDocumentURL(page);
    expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarClosed = await boSearchAliasPage.closeHelpSideBar(page);
    expect(isHelpSidebarClosed).to.eq(true);
  });
});
