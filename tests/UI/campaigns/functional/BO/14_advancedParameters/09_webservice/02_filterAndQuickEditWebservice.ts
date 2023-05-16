// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import login steps
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import dashboardPage from '@pages/BO/dashboard';
import webservicePage from '@pages/BO/advancedParameters/webservice';
import addWebservicePage from '@pages/BO/advancedParameters/webservice/add';

// Import data
import WebserviceData from '@data/faker/webservice';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_advancedParameters_webservice_filterAndQuickEditWebservice';

describe('BO - Advanced Parameters - Webservice : Filter and quick edit webservice', async () => {
  let browserContext: BrowserContext;
  let page: Page;

  let numberOfWebserviceKeys: number = 0;

  const firstWebServiceData: WebserviceData = new WebserviceData();
  const secondWebServiceData: WebserviceData = new WebserviceData();

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

  it('should go to \'Advanced parameters > Webservice\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToWebservicePage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.advancedParametersLink,
      dashboardPage.webserviceLink,
    );
    await webservicePage.closeSfToolBar(page);

    const pageTitle = await webservicePage.getPageTitle(page);
    await expect(pageTitle).to.contains(webservicePage.pageTitle);
  });

  it('should reset all filters and get number of webservices', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'firstReset', baseContext);

    numberOfWebserviceKeys = await webservicePage.resetAndGetNumberOfLines(page);
    if (numberOfWebserviceKeys !== 0) await expect(numberOfWebserviceKeys).to.be.above(0);
  });

  const tests = [
    {args: {webserviceToCreate: firstWebServiceData}},
    {args: {webserviceToCreate: secondWebServiceData}},
  ];

  tests.forEach((test: {args: {webserviceToCreate: WebserviceData}}, index: number) => {
    it('should go to add new webservice key page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToAddNewWebserviceKeyPage_${index}`, baseContext);

      await webservicePage.goToAddNewWebserviceKeyPage(page);

      const pageTitle = await addWebservicePage.getPageTitle(page);
      await expect(pageTitle).to.contains(addWebservicePage.pageTitleCreate);
    });

    it('should create webservice key', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createWebserviceKey_${index}`, baseContext);

      const textResult = await addWebservicePage.createEditWebservice(
        page,
        test.args.webserviceToCreate,
        false,
      );
      await expect(textResult).to.equal(addWebservicePage.successfulCreationMessage);

      const numberOfWebserviceKeysAfterCreation = await webservicePage.getNumberOfElementInGrid(page);
      await expect(numberOfWebserviceKeysAfterCreation).to.be.equal(numberOfWebserviceKeys + 1 + index);
    });
  });
  describe('Filter webservice table', async () => {
    const testsFilter = [
      {
        args: {
          identifier: 'filterByKey',
          filterType: 'input',
          filterBy: 'key',
          filterValue: firstWebServiceData.key,
        },
      },
      {
        args: {
          identifier: 'filterByDescription',
          filterType: 'input',
          filterBy: 'description',
          filterValue: firstWebServiceData.keyDescription,
        },
      },
      {
        args: {
          identifier: 'filterByStatus',
          filterType: 'select',
          filterBy: 'active',
          filterValue: firstWebServiceData.status ? '1' : '0',
        },
      },
    ];

    testsFilter.forEach((
      test: {args: {identifier: string, filterType: string, filterBy: string, filterValue: string, }},
      index: number,
    ) => {
      it(`should filter list by ${test.args.filterBy}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.identifier, baseContext);

        await webservicePage.filterWebserviceTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfElementAfterFilter = await webservicePage.getNumberOfElementInGrid(page);

        for (let i = 1; i <= numberOfElementAfterFilter; i++) {
          if (test.args.filterBy === 'active') {
            const wenServiceStatus = await webservicePage.getStatus(page, i);
            await expect(wenServiceStatus).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await webservicePage.getTextColumnFromTable(page, i, test.args.filterBy);
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset filter and check the number of webservice keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilter_${index}`, baseContext);

        const numberOfElement = await webservicePage.resetAndGetNumberOfLines(page);
        await expect(numberOfElement).to.be.equal(numberOfWebserviceKeys + 2);
      });
    });
  });

  describe('Quick edit webservice', async () => {
    it('should filter list by key description', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await webservicePage.filterWebserviceTable(
        page,
        'input',
        'description',
        firstWebServiceData.keyDescription,
      );

      const key = await webservicePage.getTextColumnFromTable(page, 1, 'description');
      await expect(key).to.contains(firstWebServiceData.keyDescription);
    });

    const statuses = [
      {args: {status: 'disable', enable: false}},
      {args: {status: 'enable', enable: true}},
    ];

    statuses.forEach((webservice: {args: {status: string, enable: boolean}}) => {
      it(`should ${webservice.args.status} the webservice`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${webservice.args.status}Webservice`, baseContext);

        const isActionPerformed = await webservicePage.setStatus(
          page,
          1,
          webservice.args.enable,
        );

        if (isActionPerformed) {
          const resultMessage = await webservicePage.getValidationMessage(page);
          await expect(resultMessage).to.contains(webservicePage.successfulUpdateStatusMessage);
        }

        const webserviceStatus = await webservicePage.getStatus(page, 1);
        await expect(webserviceStatus).to.be.equal(webservice.args.enable);
      });
    });
  });

  describe('Delete the created webservice keys', async () => {
    const testsDelete = [
      {args: {name: 'first'}},
      {args: {name: 'second'}},
    ];

    testsDelete.forEach((test: { args: { name:string }}, index: number) => {
      it(`should delete the ${test.args.name} webservice key created`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteWebserviceKey_${index}`, baseContext);

        const textResult = await webservicePage.deleteWebserviceKey(page, 1);
        await expect(textResult).to.equal(webservicePage.successfulDeleteMessage);
      });

      it('should reset filter and check the number of webservice keys', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFilterAfterDelete_${index}`, baseContext);

        const numberOfElement = await webservicePage.resetAndGetNumberOfLines(page);
        await expect(numberOfElement).to.be.equal(numberOfWebserviceKeys - index + 1);
      });
    });
  });
});
