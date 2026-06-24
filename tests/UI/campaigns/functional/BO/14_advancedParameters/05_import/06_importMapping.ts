// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boImportPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_import_importMapping';

/*
Save a column-matching configuration on step 2, log out and back in, then check
the saved configuration is offered again and can be reloaded. Protects the
Step-1 -> Step-2 data-matching persistence the migration will reshape.
 */
describe('BO - Advanced Parameters - Import : Save and reload a data matching configuration', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const fileName: string = 'ui_import_mapping.csv';
  const mappingName: string = 'UIImportMapping';
  const csvContent: string = 'Category ID;Active (0/1);Name *;Parent category;Root category (0/1);Description;Meta title;Meta description;URL rewritten;Image URL\n'
    + ';1;UI Mapping Category;Home;0;;;;;\n';

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);

    await utilsFile.createFile('.', fileName, csvContent);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
    await utilsFile.deleteFile(fileName);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should go to \'Advanced Parameters > Import\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToImportPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.importLink,
    );
    await boImportPage.closeSfToolBar(page);

    const pageTitle = await boImportPage.getPageTitle(page);
    expect(pageTitle).to.contains(boImportPage.pageTitle);
  });

  it('should upload the categories file and go to the data-matching step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uploadAndNext', baseContext);

    const uploadSuccessText = await boImportPage.uploadImportFile(page, 'Categories', fileName);
    expect(uploadSuccessText).to.contains(fileName);

    const panelTitle = await boImportPage.goToImportNextStep(page);
    expect(panelTitle).to.contains(boImportPage.importPanelTitle);
  });

  it('should save the data matching configuration', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'saveMapping', baseContext);

    await boImportPage.saveDataMatchingConfig(page, mappingName);

    const isVisible = await boImportPage.isLoadDataMatchingConfigVisible(page);
    expect(isVisible).to.be.eq(true);
  });

  it('should log out then log back in', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'logoutLogin', baseContext);

    await boDashboardPage.logoutBO(page);

    const pageTitle = await boLoginPage.getPageTitle(page);
    expect(pageTitle).to.contains(boLoginPage.pageTitle);

    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const dashboardTitle = await boDashboardPage.getPageTitle(page);
    expect(dashboardTitle).to.contains(boDashboardPage.pageTitle);
  });

  it('should re-upload the file and reach the data-matching step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'reUpload', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.advancedParametersLink,
      boDashboardPage.importLink,
    );
    await boImportPage.closeSfToolBar(page);

    const uploadSuccessText = await boImportPage.uploadImportFile(page, 'Categories', fileName);
    expect(uploadSuccessText).to.contains(fileName);

    const panelTitle = await boImportPage.goToImportNextStep(page);
    expect(panelTitle).to.contains(boImportPage.importPanelTitle);
  });

  it('should find the saved data matching configuration', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkSavedMapping', baseContext);

    const isVisible = await boImportPage.isLoadDataMatchingConfigVisible(page);
    expect(isVisible).to.be.eq(true);

    const configs = await boImportPage.getDataMatchingConfigs(page);
    expect(configs).to.contains(mappingName);
  });

  it('should reload the saved data matching configuration', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loadMapping', baseContext);

    await boImportPage.loadDataMatchingConfig(page, mappingName);

    const panelTitle = await boImportPage.getPageTitle(page);
    expect(panelTitle).to.contains(boImportPage.pageTitle);
  });

  it('should delete the saved data matching configuration', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'deleteMapping', baseContext);

    await boImportPage.deleteDataMatchingConfig(page);

    const configs = await boImportPage.getDataMatchingConfigs(page);
    expect(configs).to.not.contains(mappingName);
  });
});
