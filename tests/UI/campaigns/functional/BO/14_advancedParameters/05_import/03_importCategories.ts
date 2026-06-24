// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boCategoriesPage,
  boDashboardPage,
  boImportPage,
  boLoginPage,
  type BrowserContext,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_import_importCategories';

/*
Upload a categories CSV, run the import (validate + import phases) and assert the
new categories appear on the Categories listing. Exercises the modern Importer
through the legacy Step-1 -> Step-2 -> progress-modal UI contract.
 */
describe('BO - Advanced Parameters - Import : Import categories', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const fileName: string = 'ui_import_categories.csv';
  const firstCategory: string = 'UI Import Category A';
  const secondCategory: string = 'UI Import Category B';
  const csvContent: string = 'Category ID;Active (0/1);Name *;Parent category;Root category (0/1);'
    + 'Description;Meta title;Meta description;URL rewritten;Image URL\n'
    + `;1;${firstCategory};Home;0;;;;;\n`
    + `;1;${secondCategory};Home;0;;;;;\n`;

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

  it('should upload the categories file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uploadFile', baseContext);

    const uploadSuccessText = await boImportPage.uploadImportFile(page, 'Categories', fileName);
    expect(uploadSuccessText).to.contains(fileName);
  });

  it('should go to the data-matching step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'nextStep', baseContext);

    const panelTitle = await boImportPage.goToImportNextStep(page);
    expect(panelTitle).to.contains(boImportPage.importPanelTitle);
  });

  it('should start the import', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'startImport', baseContext);

    const modalTitle = await boImportPage.startFileImport(page);
    expect(modalTitle).to.contains(boImportPage.importModalTitle);
  });

  it('should check that the import is completed', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'waitForImport', baseContext);

    const isCompleted = await boImportPage.getImportValidationMessage(page);
    expect(isCompleted, 'The import is not completed!').to.contains('Data imported');
  });

  it('should close the import progress modal', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'closeModal', baseContext);

    const isModalClosed = await boImportPage.closeImportModal(page);
    expect(isModalClosed).to.be.eq(true);
  });

  it('should go to \'Catalog > Categories\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCategoriesPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.categoriesLink,
    );

    const pageTitle = await boCategoriesPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCategoriesPage.pageTitle);
  });

  it('should filter the listing and find the first imported category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterFirstCategory', baseContext);

    await boCategoriesPage.resetFilter(page);
    await boCategoriesPage.filterCategories(page, 'input', 'name', firstCategory);

    const numberOfCategories = await boCategoriesPage.getNumberOfElementInGrid(page);
    expect(numberOfCategories).to.be.eq(1);

    const categoryName = await boCategoriesPage.getTextColumnFromTableCategories(page, 1, 'name');
    expect(categoryName).to.contains(firstCategory);
  });

  it('should filter the listing and find the second imported category', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterSecondCategory', baseContext);

    await boCategoriesPage.resetFilter(page);
    await boCategoriesPage.filterCategories(page, 'input', 'name', secondCategory);

    const numberOfCategories = await boCategoriesPage.getNumberOfElementInGrid(page);
    expect(numberOfCategories).to.be.eq(1);

    await boCategoriesPage.resetFilter(page);
  });
});
