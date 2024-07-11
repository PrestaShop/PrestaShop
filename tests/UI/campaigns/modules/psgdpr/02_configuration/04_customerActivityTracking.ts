// Import utils
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
// Import FO pages
import {myAccountPage} from '@pages/FO/classic/myAccount';
import {createAccountPage as foCreateAccountPage} from '@pages/FO/classic/myAccount/add';
// Import BO pages
import psGdpr from '@pages/BO/modules/psGdpr';
import psGdprTabCustomerActivity from '@pages/BO/modules/psGdpr/tabCustomerActivity';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {
  boDashboardPage,
  boModuleManagerPage,
  dataCustomers,
  dataModules,
  FakerCustomer,
  foClassicHomePage,
  foClassicLoginPage,
  utilsCore,
  utilsFile,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';
import {gdprPersonalDataPage} from '@pages/FO/classic/myAccount/gdprPersonalData';

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
      await loginCommon.loginBO(this, page);
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

      const pageTitle = await psGdpr.getPageSubtitle(page);
      expect(pageTitle).to.eq(psGdpr.pageSubTitle);
    });

    it('should display the tab "Customer activity tracking"', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayTabCustomerActivity', baseContext);

      const isTabVisible = await psGdpr.goToTab(page, 4);
      expect(isTabVisible).to.be.equals(true);
    });

    it('should check the Customer Activity list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerActivityList', baseContext);

      const numRows = await psGdprTabCustomerActivity.getNumberOfElementInGrid(page);
      expect(numRows).to.equal(1);
    });

    it('should go to FO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToFo', baseContext);

      // View my shop and get the new tab
      page = await psGdprTabCustomerActivity.viewMyShop(page);

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

      const pageTitle = await myAccountPage.getPageTitle(page);
      expect(pageTitle).to.contains(myAccountPage.pageTitle);
    });

    it('should go to \'GDPR - Personal data\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToGDPRPage1', baseContext);

      await myAccountPage.goToMyGDPRPersonalDataPage(page);

      const pageTitle = await gdprPersonalDataPage.getPageTitle(page);
      expect(pageTitle).to.equal(gdprPersonalDataPage.pageTitle);
    });

    it('should click on \'Get my data to CSV file\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToCSV', baseContext);

      const filePath = await gdprPersonalDataPage.exportDataToCSV(page);

      const found = await utilsFile.doesFileExist(filePath);
      expect(found).to.equal(true);

      await utilsFile.deleteFile(filePath);
    });

    it('should click on \'Get my data to PDF file\'', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnGetMyDataToPDF', baseContext);

      await page.waitForTimeout(3000);
      const filePath = await gdprPersonalDataPage.exportDataToPDF(page);

      const found = await utilsFile.doesFileExist(filePath);
      expect(found).to.equal(true);
    });

    it('should logout', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'foLogout', baseContext);

      await gdprPersonalDataPage.logout(page);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected, 'Customer is connected').to.eq(false);
    });

    it('should check the Customer Activity list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerActivityListAfterRequest', baseContext);

      page = await foClassicHomePage.changePage(browserContext, 0);
      await psGdprTabCustomerActivity.reloadPage(page);

      const numRows = await psGdprTabCustomerActivity.getNumberOfElementInGrid(page);
      expect(numRows).to.equal(3);

      const row1Name = await psGdprTabCustomerActivity.getTextColumnFromTable(page, 1, 1);
      expect(row1Name).to.equal(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);

      const row1Type = await psGdprTabCustomerActivity.getTextColumnFromTable(page, 1, 2);
      expect(row1Type).to.equal('Accessibility (pdf)');

      const row2Name = await psGdprTabCustomerActivity.getTextColumnFromTable(page, 2, 1);
      expect(row2Name).to.equal(`${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`);

      const row2Type = await psGdprTabCustomerActivity.getTextColumnFromTable(page, 2, 2);
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

      const pageHeaderTitle = await foCreateAccountPage.getHeaderTitle(page);
      expect(pageHeaderTitle).to.equal(foCreateAccountPage.formTitle);
    });

    it('should create new account', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'createNewAccount', baseContext);

      await foCreateAccountPage.createAccount(page, customerData);

      const isCustomerConnected = await foClassicHomePage.isCustomerConnected(page);
      expect(isCustomerConnected).to.eq(true);
    });

    it('should check the Customer Activity list', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCustomerActivityListAfterRegistration', baseContext);

      page = await foClassicHomePage.changePage(browserContext, 0);
      await psGdprTabCustomerActivity.reloadPage(page);

      const numRows = await psGdprTabCustomerActivity.getNumberOfElementInGrid(page);
      expect(numRows).to.equal(4);

      const row1Name = await psGdprTabCustomerActivity.getTextColumnFromTable(page, 1, 1);
      expect(row1Name).to.equal(`${customerData.firstName} ${customerData.lastName}`);

      const row1Type = await psGdprTabCustomerActivity.getTextColumnFromTable(page, 1, 2);
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

        const nonSortedTable = await psGdprTabCustomerActivity.getAllRowsColumnContent(page, arg.sortColNth);
        await psGdprTabCustomerActivity.sortTable(page, arg.sortColNth, arg.sortOrder);
        const sortedTable = await psGdprTabCustomerActivity.getAllRowsColumnContent(page, arg.sortColNth);

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

      const rowsNumber: number = await psGdprTabCustomerActivity.getNumberOfElementInGrid(page);

      for (let i = 1; i <= rowsNumber; i++) {
        clipboardExpected += await psGdprTabCustomerActivity.getTextColumnFromTable(page, i, 1);
        clipboardExpected += '\t';
        clipboardExpected += await psGdprTabCustomerActivity.getTextColumnFromTable(page, i, 2);
        clipboardExpected += '\t';
        clipboardExpected += await psGdprTabCustomerActivity.getTextColumnFromTable(page, i, 3);
        if (i < rowsNumber) {
          clipboardExpected += '\n';
        }
      }

      const result = await psGdprTabCustomerActivity.copyTable(page);
      expect(result).to.be.equal(clipboardExpected);
    });

    it('should click on the "Excel" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkExcelButton', baseContext);

      const filePath = await psGdprTabCustomerActivity.exportTable(page, 'excel');

      const hasFoundFile = await utilsFile.doesFileExist(filePath);
      expect(hasFoundFile).to.equals(true);

      const rowsNumber: number = await psGdprTabCustomerActivity.getNumberOfElementInGrid(page);

      for (let iRow = 1; iRow <= rowsNumber; iRow++) {
        for (let iCol = 1; iCol <= 3; iCol++) {
          const colContent = await psGdprTabCustomerActivity.getTextColumnFromTable(page, iRow, iCol);
          const cellContent = await utilsFile.getTextInXLSX(filePath, 2 + iRow, iCol);
          expect(colContent).to.equal(cellContent);
        }
      }
    });

    it('should click on the "CSV" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkCSVButton', baseContext);

      const filePath = await psGdprTabCustomerActivity.exportTable(page, 'csv');

      const hasFoundFile = await utilsFile.doesFileExist(filePath);
      expect(hasFoundFile).to.equals(true);

      const rowsNumber: number = await psGdprTabCustomerActivity.getNumberOfElementInGrid(page);

      const hasHeaders = await utilsFile.isTextInFile(filePath, '"Client name/ID","Type of request","Submission date"');
      expect(hasHeaders).to.equal(true);

      for (let i = 1; i <= rowsNumber; i++) {
        const hasRow = await utilsFile.isTextInFile(
          filePath,
          `"${await psGdprTabCustomerActivity.getTextColumnFromTable(page, i, 1)}",`
          + `"${await psGdprTabCustomerActivity.getTextColumnFromTable(page, i, 2)}",`
          + `"${await psGdprTabCustomerActivity.getTextColumnFromTable(page, i, 3)}"`,
        );
        expect(hasRow).to.equal(true);
      }
    });

    it('should click on the "PDF" button', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkPDFButton', baseContext);

      const filePath = await psGdprTabCustomerActivity.exportTable(page, 'pdf');

      const hasFoundFile = await utilsFile.doesFileExist(filePath);
      expect(hasFoundFile).to.equals(true);

      const rowsNumber: number = await psGdprTabCustomerActivity.getNumberOfElementInGrid(page);

      const hasHeaders = await utilsFile.isTextInPDF(filePath, 'Client, ,name/,ID, ,Type, ,of, ,request, ,Submission, ,date');
      expect(hasHeaders).to.equal(true);

      for (let i = 1; i <= rowsNumber; i++) {
        const hasRow = await utilsFile.isTextInPDF(
          filePath,
          `${(await psGdprTabCustomerActivity.getTextColumnFromTable(page, i, 1)).replace(' ', ', ,')}, ,`
          + `${(await psGdprTabCustomerActivity.getTextColumnFromTable(page, i, 2)).replace(' ', ', ,')}, ,`
          + `${(await psGdprTabCustomerActivity.getTextColumnFromTable(page, i, 3)).replace(' ', ', ,')}`,
        );
        expect(hasRow).to.equal(true);
      }
    });
  });
});
