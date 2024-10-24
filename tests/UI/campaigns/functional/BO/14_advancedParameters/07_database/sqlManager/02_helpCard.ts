// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boLoginPage,
  boSqlManagerPage,
  type BrowserContext,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_database_sqlManager_helpCard';

// Check that help card is in english in sql manager page
describe('BO - Advanced Parameters - Database : Help card in SQL Manager page', async () => {
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

  it('should go to \'Advanced Parameters > Database\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.databaseLink,
    );
    await boSqlManagerPage.closeSfToolBar(page);

    const pageTitle = await boSqlManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(boSqlManagerPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await boSqlManagerPage.openHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);

    const documentURL = await boSqlManagerPage.getHelpDocumentURL(page);
    expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarVisible = await boSqlManagerPage.closeHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);
  });
});
