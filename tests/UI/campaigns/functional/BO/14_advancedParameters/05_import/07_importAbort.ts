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

const baseContext: string = 'functional_BO_advancedParameters_import_importAbort';

/*
Start a long import then click "Abort import": the progress modal must show the
aborting state and stop cleanly. Protects the progress-modal / AJAX-poller UI
contract that the migration will reshape.
 */
describe('BO - Advanced Parameters - Import : Abort a running import', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  const numberOfProducts: number = 300;
  const fileName: string = 'ui_import_abort.csv';

  let csvContent: string = 'Active (0/1);Name *;Categories (x,y,z...);Price tax excluded;Reference #;Quantity\n';

  for (let i = 1; i <= numberOfProducts; i++) {
    csvContent += `1;UI Abort Product ${i};Home;${i}.99;UI-ABORT-${i};10\n`;
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

  it('should upload the products file and go to the data-matching step', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'uploadAndNext', baseContext);

    const uploadSuccessText = await boImportPage.uploadImportFile(page, 'Products', fileName);
    expect(uploadSuccessText).to.contains(fileName);

    const panelTitle = await boImportPage.goToImportNextStep(page);
    expect(panelTitle).to.contains(boImportPage.importPanelTitle);
  });

  it('should start the import', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'startImport', baseContext);

    const modalTitle = await boImportPage.startFileImport(page);
    expect(modalTitle).to.contains(boImportPage.importModalTitle);
  });

  it('should abort the running import', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'abortImport', baseContext);

    const isAborting = await boImportPage.abortImport(page);
    expect(isAborting, 'The aborting state was not shown').to.be.eq(true);
  });
});
