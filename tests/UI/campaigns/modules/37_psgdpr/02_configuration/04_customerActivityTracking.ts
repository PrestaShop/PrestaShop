import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boLoginPage,
  boModuleManagerPage,
  type BrowserContext,
  dataCustomers,
  dataModules,
  FakerCustomer,
  foClassicCreateAccountPage,
  foClassicHomePage,
  foClassicLoginPage,
  foClassicMyAccountPage,
  foClassicMyGDPRPersonalDataPage,
  modPsGdprBoMain,
  modPsGdprBoTabCustomerActivity,
  type Page,
  utilsCore,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'modules_psgdpr_configuration_customerActivityTracking';

describe('BO - Modules - GDPR: Customer activity tracking', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  const customerData: FakerCustomer = new FakerCustomer();

  describe('Customer activity tracking', async () => {
    before(async function () {
      browserContext = await utilsPlaywright.createBrowserContext(this.browser);
      page = await utilsPlaywright.newTab(browserContext);
    });

    after(async () => {
      await utilsPlaywright.closeBrowserContext(browserContext);
    });

    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Modules > Module Manager\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToModuleManagerPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.modulesParentLink,
        boDashboardPage.moduleManagerLink,
      );
      await boModuleManagerPage.closeSfToolBar(page);

      const pageTitle = await boModuleManagerPage.getPageTitle(page);
      expect(pageTitle).to.contains(boModuleManagerPage.pageTitle);
    });

    it(`should search the module ${dataModules.psGdpr.name}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchModule', baseContext);

      const isModuleVisible = await boModuleManagerPage.searchModule(page, dataModules.psGdpr);
      expect(isModuleVisible).to.eq(true);
    });

    it(`should go to the configuration page of the module '${dataModules.psGdpr.name}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToConfigurationPage', baseContext);

      await boModuleManagerPage.goToConfigurationPage(page, dataModules.psGdpr.tag);

      const pageTitle = await modPsGdprBoMain.getPageSubtitle(page);
      expect(pageTitle).to.eq(modPsGdprBoMain.pageSubTitle);
    });

    it('should display the tab "Customer activity tracking"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayTabCustomerActivity', baseContext);

      const isTabVisible = await modPsGdprBoMain.goToTab(page, 4);
      expect(isTabVisible).to.be.equals(true);
    });

    it('should check the Customer Activity list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerActivityList', baseContext);

      const numRows = await modPsGdprBoTabCustomerActivity.getNumberOfElementInGrid(page);
      expect(numRows).to.equal(1);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      // View my shop and get the new tab
      page = await modPsGdprBoTabCustomerActivity.viewMyShop(page);

      const isHomePage = await foClassicHomePage.isHomePage(page);
      expect(isHomePage).to.equal(true);
    });

    it('should click on the \'Sign in\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSignInLink', baseContext);

      // Check sign in link
      await foClassicHomePage.clickOnHeaderLink(page, 'Sign in');

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should login', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogin', baseContext);

      await foClassicLoginPage.customerLogin(page, dataCustomers.johnDoe);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should go to account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToAccountPage', baseContext);

      await foClassicHomePage.goToMyAccountPage(page);

      const pageTitle = await foClassicMyAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(foClassicMyAccountPage.pageTitle);
    });

    it('should go to \'GDPR - Personal data\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage1', baseContext);

      await foClassicMyAccountPage.goToMyGDPRPersonalDataPage(page);

      const pageTitle = await foClassicMyGDPRPersonalDataPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicMyGDPRPersonalDataPage.pageTitle);
    });

    it('should click on \'Get my data to CSV file\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV', baseContext);

      const filePath = await foClassicMyGDPRPersonalDataPage.exportDataToCSV(page);

      const found = await utilsFile.doesFileExist(filePath);
      expect(found).to.equal(true);

      await utilsFile.deleteFile(filePath);
    });

    it('should click on \'Get my data to PDF file\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToPDF', baseContext);

      await page.waitForTimeout(3000);
      const filePath = await foClassicMyGDPRPersonalDataPage.exportDataToPDF(page);

      const found = await utilsFile.doesFileExist(filePath);
      expect(found).to.equal(true);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogout', baseContext);

      await foClassicMyGDPRPersonalDataPage.logout(page);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should check the Customer Activity list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerActivityListAfterRequest', baseContext);

      page = await foClassicHomePage.changePage(browserContext, 0);
      await modPsGdprBoTabCustomerActivity.reloadPage(page);

      const numRows = await modPsGdprBoTabCustomerActivity.getNumberOfElementInGrid(page);
      expect(numRows).to.equal(3);

      const row1Name = await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, 1, 1);
      expect(row1Name).to.equal(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);

      const row1Type = await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, 1, 2);
      expect(row1Type).to.equal('Accessibility (pdf)');

      const row2Name = await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, 2, 1);
      expect(row2Name).to.equal(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);

      const row2Type = await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, 2, 2);
      expect(row2Type).to.equal('Accessibility (csv)');
    });

    it('should click on the \'Sign in\' link', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnSignInLinkForRegister', baseContext);

      page = await foClassicHomePage.changePage(browserContext, 1);
      await foClassicHomePage.clickOnHeaderLink(page, 'Sign in');

      const pageTitle = await foClassicLoginPage.getPageTitle(page);
      expect(pageTitle).to.equal(foClassicLoginPage.pageTitle);
    });

    it('should go to create account page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateAccountPage', baseContext);

      await foClassicLoginPage.goToCreateAccountPage(page);

      const pageHeaderTitle = await foClassicCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foClassicCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAccount', baseContext);

      await foClassicCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should check the Customer Activity list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerActivityListAfterRegistration', baseContext);

      page = await foClassicHomePage.changePage(browserContext, 0);
      await modPsGdprBoTabCustomerActivity.reloadPage(page);

      const numRows = await modPsGdprBoTabCustomerActivity.getNumberOfElementInGrid(page);
      expect(numRows).to.equal(4);

      const row1Name = await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, 1, 1);
      expect(row1Name).to.equal(`${customerData.firstName} ${customerData.lastName}`);

      const row1Type = await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, 1, 2);
      expect(row1Type).to.equal('Consent confirmation');
    });

    [
      {
        sortColNth: 1,
        sortName: 'Client Name/ID',
        sortOrder: 'asc',
      },
      {
        sortColNth: 1,
        sortName: 'Client Name/ID',
        sortOrder: 'desc',
      },
      {
        sortColNth: 2,
        sortName: 'Type of request',
        sortOrder: 'asc',
      },
      {
        sortColNth: 2,
        sortName: 'Type of request',
        sortOrder: 'desc',
      },
      {
        sortColNth: 3,
        sortName: 'Submission date',
        sortOrder: 'asc',
      },
      {
        sortColNth: 3,
        sortName: 'Submission date',
        sortOrder: 'desc',
      },
    ].forEach((arg: {sortColNth: number, sortName: string, sortOrder: string}) => {
      it(`should sort by ${arg.sortName} ${arg.sortOrder.toUpperCase()}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `sort_${arg.sortColNth}_${arg.sortOrder}`, baseContext);

        const nonSortedTable = await modPsGdprBoTabCustomerActivity.getAllRowsColumnContent(page, arg.sortColNth);
        await modPsGdprBoTabCustomerActivity.sortTable(page, arg.sortColNth, arg.sortOrder);
        const sortedTable = await modPsGdprBoTabCustomerActivity.getAllRowsColumnContent(page, arg.sortColNth);

        const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

        if (arg.sortOrder === 'asc') {
          expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });

    it('should click on the "Copy" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCopyButton', baseContext);

      let clipboardExpected: string = `Module Manager • ${global.INSTALL.SHOP_NAME}\n`
          + '\n'
          + 'Client name/ID\tType of request\tSubmission date\n';

      const rowsNumber: number = await modPsGdprBoTabCustomerActivity.getNumberOfElementInGrid(page);

      for (let i = 1; i <= rowsNumber; i++) {
        clipboardExpected += await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, i, 1);
        clipboardExpected += '\t';
        clipboardExpected += await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, i, 2);
        clipboardExpected += '\t';
        clipboardExpected += await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, i, 3);
        if (i < rowsNumber) {
          clipboardExpected += '\n';
        }
      }

      const result = await modPsGdprBoTabCustomerActivity.copyTable(page);
      expect(result).to.be.equal(clipboardExpected);
    });

    it('should click on the "Excel" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExcelButton', baseContext);

      const filePath = await modPsGdprBoTabCustomerActivity.exportTable(page, 'excel');

      const hasFoundFile = await utilsFile.doesFileExist(filePath);
      expect(hasFoundFile).to.equals(true);

      const rowsNumber: number = await modPsGdprBoTabCustomerActivity.getNumberOfElementInGrid(page);

      for (let iRow = 1; iRow <= rowsNumber; iRow++) {
        for (let iCol = 1; iCol <= 3; iCol++) {
          const colContent = await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, iRow, iCol);
          const cellContent = await utilsFile.getTextInXLSX(filePath, 2 + iRow, iCol);
          expect(colContent).to.equal(cellContent);
        }
      }
    });

    it('should click on the "CSV" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCSVButton', baseContext);

      const filePath = await modPsGdprBoTabCustomerActivity.exportTable(page, 'csv');

      const hasFoundFile = await utilsFile.doesFileExist(filePath);
      expect(hasFoundFile).to.equals(true);

      const rowsNumber: number = await modPsGdprBoTabCustomerActivity.getNumberOfElementInGrid(page);

      const hasHeaders = await utilsFile.isTextInFile(filePath, '"Client name/ID","Type of request","Submission date"');
      expect(hasHeaders).to.equal(true);

      for (let i = 1; i <= rowsNumber; i++) {
        const hasRow = await utilsFile.isTextInFile(
          filePath,
          `"${await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, i, 1)}",`
          + `"${await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, i, 2)}",`
          + `"${await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, i, 3)}"`,
        );
        expect(hasRow).to.equal(true);
      }
    });

    it('should click on the "PDF" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPDFButton', baseContext);

      const filePath = await modPsGdprBoTabCustomerActivity.exportTable(page, 'pdf');

      const hasFoundFile = await utilsFile.doesFileExist(filePath);
      expect(hasFoundFile).to.equals(true);

      const rowsNumber: number = await modPsGdprBoTabCustomerActivity.getNumberOfElementInGrid(page);

      const hasHeaders = await utilsFile.isTextInPDF(filePath, 'Client, ,name/,ID, ,Type, ,of, ,request, ,Submission, ,date');
      expect(hasHeaders).to.equal(true);

      for (let i = 1; i <= rowsNumber; i++) {
        const hasRow = await utilsFile.isTextInPDF(
          filePath,
          `${(await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, i, 1)).replace(' ', ', ,')}, ,`
          + `${(await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, i, 2)).replace(' ', ', ,')}, ,`
          + `${(await modPsGdprBoTabCustomerActivity.getTextColumnFromTable(page, i, 3)).replace(' ', ', ,')}`,
        );
        expect(hasRow).to.equal(true);
      }
    });
  });
});
