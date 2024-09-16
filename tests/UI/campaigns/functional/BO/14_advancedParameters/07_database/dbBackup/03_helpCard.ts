// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import sqlManagerPage from '@pages/BO/advancedParameters/database/sqlManager';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boDbBackupPage,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_database_dbBackup_helpCard';

// Check that help card is in english in dbBackup page
describe('BO - Advanced Parameters - Database : Help card in DB Backup page', async () => {
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

  it('should go to \'Advanced Parameters > Database\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToSqlManagerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.databaseLink,
    );

    await sqlManagerPage.closeSfToolBar(page);

    const pageTitle = await sqlManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(sqlManagerPage.pageTitle);
  });

  it('should go to \'DB Backup\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToDbBackupPage', baseContext);

    await sqlManagerPage.goToDbBackupPage(page);

    const pageTitle = await boDbBackupPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDbBackupPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await boDbBackupPage.openHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);

    const documentURL = await boDbBackupPage.getHelpDocumentURL(page);
    expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarNotVisible = await boDbBackupPage.closeHelpSideBar(page);
    expect(isHelpSidebarNotVisible).to.eq(true);
  });
});
