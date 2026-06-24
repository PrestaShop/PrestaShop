// Import utils
import testContext from '@utils/testContext';

import {expect} from 'chai';
import {
  boDashboardPage,
  boImportPage,
  boLoginPage,
  boProductsPage,
  type BrowserContext,
  type Page,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_advancedParameters_import_importProducts';

/*
Upload a products CSV, run the import and assert the new products appear on the
Products listing, filtered by reference. Exercises the modern Importer through
the Step-1 -> Step-2 -> progress-modal UI contract.
 */
describe('BO - Advanced Parameters - Import : Import products', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const fileName: string = 'ui_import_products.csv';
  const firstReference: string = 'UI-IMP-A';
  const secondReference: string = 'UI-IMP-B';
  const csvContent: string = 'Active (0/1);Name *;Categories (x,y,z...);Price tax excluded;Reference #;Quantity\n'
    + `1;UI Import Product A;Home;19.99;${firstReference};100\n`
    + `1;UI Import Product B;Home;29.99;${secondReference};50\n`;

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

  it('should upload the products file', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uploadFile', baseContext);

    const uploadSuccessText = await boImportPage.uploadImportFile(page, 'Products', fileName);
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

  it('should go to \'Catalog > Products\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.productsLink,
    );
    await boProductsPage.closeSfToolBar(page);

    const pageTitle = await boProductsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boProductsPage.pageTitle);
  });

  it('should filter the listing and find the first imported product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterFirstProduct', baseContext);

    await boProductsPage.resetFilter(page);
    await boProductsPage.filterProducts(page, 'reference', firstReference, 'input');

    const numberOfProducts = await boProductsPage.getNumberOfProductsFromList(page);
    expect(numberOfProducts).to.be.eq(1);

    const reference = await boProductsPage.getTextColumn(page, 'reference', 1);
    expect(reference).to.contains(firstReference);
  });

  it('should filter the listing and find the second imported product', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'filterSecondProduct', baseContext);

    await boProductsPage.resetFilter(page);
    await boProductsPage.filterProducts(page, 'reference', secondReference, 'input');

    const numberOfProducts = await boProductsPage.getNumberOfProductsFromList(page);
    expect(numberOfProducts).to.be.eq(1);

    await boProductsPage.resetFilter(page);
  });
});
