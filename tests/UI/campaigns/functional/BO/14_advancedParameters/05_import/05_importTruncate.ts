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

const baseContext: string = 'functional_BO_advancedParameters_import_importTruncate';

/*
Import 10 products with the "truncate" option enabled and assert the Products
listing then contains exactly 10 products (all pre-existing products removed).
Destructive by design - meant to run against a fresh shop.
 */
describe('BO - Advanced Parameters - Import : Import with truncate', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const numberOfProducts: number = 10;
  const fileName: string = 'ui_import_truncate.csv';

  // Columns must follow the import field order (positional auto-mapping): id, active, name, category, price.
  let csvContent: string = 'Product ID;Active (0/1);Name *;Categories (x,y,z...);Price tax excluded\n';

  for (let i = 1; i <= numberOfProducts; i++) {
    csvContent += `;1;UI Truncate Product ${i};Home;${i}.99\n`;
  }

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

  it('should upload the products file and enable truncate', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uploadFile', baseContext);

    const uploadSuccessText = await boImportPage.uploadImportFile(page, 'Products', fileName);
    expect(uploadSuccessText).to.contains(fileName);

    await boImportPage.setTruncate(page, true);
  });

  it('should go to the data-matching step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'nextStep', baseContext);

    // Enabling truncate adds a "delete all data" confirm dialog on submit; accept it.
    await boImportPage.dialogListener(page, true);

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

  it('should check that exactly the imported products remain', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'checkProductsCount', baseContext);

    await boProductsPage.resetFilter(page);

    const productsCount = await boProductsPage.getNumberOfProductsFromHeader(page);
    expect(productsCount).to.be.eq(numberOfProducts);
  });
});
