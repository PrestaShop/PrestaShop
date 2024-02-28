// Import utils
import files from '@utils/files';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import BO pages
import dashboardPage from '@pages/BO/dashboard';
import {moduleManager as moduleManagerPage} from '@pages/BO/modules/moduleManager';
import psFacetedSearch from '@pages/BO/modules/psFacetedSearch';
import psFacetedSearchFilterTemplate from '@pages/BO/modules/psFacetedSearch/filterTemplate';

// Import data
import Modules from '@data/demo/modules';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'modules_ps_facetedsearch_configuration_addNewTemplate';

describe('Faceted search module - Add new template', async () => {
  const templateName: string = 'My Template Name';

  let browserContext: BrowserContext;
  let page: Page;

  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
    await files.deleteFile('module.zip');
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
    expect(pageTitle).to.contains(moduleManagerPage.pageTitle);
  });

  it(`should search the module ${Modules.psFacetedSearch.name}`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

    const isModuleVisible = await moduleManagerPage.searchModule(page, Modules.psFacetedSearch);
    expect(isModuleVisible).to.be.eq(true);
  });

  it(`should go to the configuration page of the module '${Modules.psFacetedSearch.name}'`, async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

    await moduleManagerPage.goToConfigurationPage(page, Modules.psFacetedSearch.tag);

    const pageTitle = await psFacetedSearch.getPageSubtitle(page);
    expect(pageTitle).to.eq(psFacetedSearch.pageSubTitle);
  });

  it('should go to the "Add new template" page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAddNewTemplatePage', baseContext);

    await psFacetedSearch.goToAddNewTemplate(page);

    const pageTitle = await psFacetedSearchFilterTemplate.getPanelTitle(page);
    expect(pageTitle).to.eq(psFacetedSearchFilterTemplate.title);
  });

  it('should add a new template', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'addNewTemplate', baseContext);

    await psFacetedSearchFilterTemplate.setTemplateName(page, templateName);
    await psFacetedSearchFilterTemplate.setTemplatePages(page, ['manufacturer']);
    await psFacetedSearchFilterTemplate.setTemplateFilterForm(
      page,
      'Product stock filter',
      true,
      '',
    );

    const textResult = await psFacetedSearchFilterTemplate.saveTemplate(page);
    expect(textResult).to.contains(psFacetedSearch.msgSuccessfulCreation(templateName));
  });

  it('should delete the template', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteTemplate', baseContext);

    const textResult = await psFacetedSearch.deleteFilterTemplate(page, 1);
    expect(textResult).to.contains(psFacetedSearch.msgSuccessfulDelete);
  });
});
