// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_modules_moduleManager_modules_seeMore';

describe('BO - Modules - Module Manager : See more/less', async () => {
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

  it('should go to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.modulesParentLink,
      dashboardPage.moduleManagerLink,
    );
    await moduleManagerPage.closeSfToolBar(page);

    const pageTitle = await moduleManagerPage.getPageTitle(page);
    await expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it('should get the number of modules in module theme block', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfModules', baseContext);

    const numberOfModules = await moduleManagerPage.getNumberOfModulesInBlock(page, 'theme_modules');
    await expect(numberOfModules).to.eq(6);
  });

  it('should click on \'See more button\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnSeeMore', baseContext);

    const isSeeLessButtonVisible = await moduleManagerPage.clickOnSeeMoreButton(page, 'theme_modules');
    await expect(isSeeLessButtonVisible).to.be.true;
  });

  it('should get the number of modules in module theme block', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfModulesAfterSeeMore', baseContext);

    const numberOfModules = await moduleManagerPage.getNumberOfModulesInBlock(page, 'theme_modules');
    await expect(numberOfModules).to.be.above(6);
  });

  it('should click on \'See less button\'', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'clickOnSeeLess', baseContext);

    const isSeeMoreButtonVisible = await moduleManagerPage.clickOnSeeLessButton(page, 'theme_modules');
    await expect(isSeeMoreButtonVisible).to.be.true;
  });

  it('should get the number of modules in module theme block', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'getNumberOfModulesAfterSeeLess', baseContext);

    const numberOfModules = await moduleManagerPage.getNumberOfModulesInBlock(page, 'theme_modules');
    await expect(numberOfModules).to.eq(6);
  });
});
