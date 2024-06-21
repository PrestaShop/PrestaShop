// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  dataModules,
  modPsFacetedsearchBoFilterTemplate,
  modPsFacetedsearchBoMain,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_ps_facetedsearch_configuration_addNewTemplate';

describe('Faceted search module - Add new template', async () => {
  const templateName: string = 'My Template Name';

  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile('module.zip');
  });

  it('should login in BO', async function () {
    await loginCommon.loginBO(this, page);
  });

  it('should go to \'Modules > Module Manager\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.modulesParentLink,
      boDashboardPage.moduleManagerLink,
    );
    await moduleManagerPage.closeSfToolBar(page);

    const pageTitle = await moduleManagerPage.getPageTitle(page);
    expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it(`should search the module ${dataModules.psFacetedSearch.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, dataModules.psFacetedSearch);
    expect(isModuleVisible).to.be.eq(true);
  });

  it(`should go to the configuration page of the module '${dataModules.psFacetedSearch.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await moduleManagerPage.goToConfigurationPage(page, dataModules.psFacetedSearch.tag);

    const pageTitle = await modPsFacetedsearchBoMain.getPageSubtitle(page);
    expect(pageTitle).to.eq(modPsFacetedsearchBoMain.pageSubTitle);
  });

  it('should go to the "Add new template" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTemplatePage', baseContext);

    await modPsFacetedsearchBoMain.goToAddNewTemplate(page);

    const pageTitle = await modPsFacetedsearchBoFilterTemplate.getPanelTitle(page);
    expect(pageTitle).to.eq(modPsFacetedsearchBoFilterTemplate.title);
  });

  it('should add a new template', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addNewTemplate', baseContext);

    await modPsFacetedsearchBoFilterTemplate.setTemplateName(page, templateName);
    await modPsFacetedsearchBoFilterTemplate.setTemplatePages(page, ['manufacturer']);
    await modPsFacetedsearchBoFilterTemplate.setTemplateFilterForm(
      page,
      'Product stock filter',
      true,
      '',
    );

    const textResult = await modPsFacetedsearchBoFilterTemplate.saveTemplate(page);
    expect(textResult).to.contains(modPsFacetedsearchBoMain.msgSuccessfulCreation(templateName));
  });

  it('should delete the template', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteTemplate', baseContext);

    const textResult = await modPsFacetedsearchBoMain.deleteFilterTemplate(page, 1);
    expect(textResult).to.contains(modPsFacetedsearchBoMain.msgSuccessfulDelete);
  });
});
