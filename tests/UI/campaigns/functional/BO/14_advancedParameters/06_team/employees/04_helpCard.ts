// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import employeesPage from '@pages/BO/advancedParameters/team';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_team_employees_helpCard';

// Check that help card is in english in employees page
describe('BO - Advanced Parameters - Team : Help card in Employee page', async () => {
  let browserContext: BrowserContext;
  let page: Page;

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

  it('should go to \'Advanced Parameters > Team\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToEmployeesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.teamLink,
    );
    await employeesPage.closeSfToolBar(page);

    const pageTitle = await employeesPage.getPageTitle(page);
    expect(pageTitle).to.contains(employeesPage.pageTitle);
  });

  it('should open the help side bar and check the document language', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'openHelpSidebar', baseContext);

    const isHelpSidebarVisible = await employeesPage.openHelpSideBar(page);
    expect(isHelpSidebarVisible).to.eq(true);

    const documentURL = await employeesPage.getHelpDocumentURL(page);
    expect(documentURL).to.contains('country=en');
  });

  it('should close the help side bar', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeHelpSidebar', baseContext);

    const isHelpSidebarNotVisible = await employeesPage.closeHelpSideBar(page);
    expect(isHelpSidebarNotVisible).to.eq(true);
  });
});
