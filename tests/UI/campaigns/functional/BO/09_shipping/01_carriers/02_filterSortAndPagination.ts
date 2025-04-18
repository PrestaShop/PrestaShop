import {expect} from 'chai';
import testContext from '@utils/testContext';

import {
  boCarriersPage,
  boCarriersCreatePage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataCarriers,
  FakerCarrier,
  type Page,
  utilsCore,
  utilsFile,
  utilsPlaywright,
  dataZones,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_shipping_carriers_filterSortAndPagination';

describe('BO - Shipping - Carriers : Filter, sort and pagination carriers', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCarriers: number = 0;

  // before and after functions
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

  it('should go to \'Shipping > Carriers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToCarriersPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.shippingLink,
      boDashboardPage.carriersLink,
    );

    const pageTitle = await boCarriersPage.getPageTitle(page);
    expect(pageTitle).to.contains(boCarriersPage.pageTitle);
  });

  it('should reset all filters and get number of carriers in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfCarriers = await boCarriersPage.resetAndGetNumberOfLines(page);
    expect(numberOfCarriers).to.be.above(0);
  });

  // 1 - Filter carriers
  describe('Filter carriers table', async () => {
    [
      {
        args:
          {
            testIdentifier: 'filterById',
            filterType: 'input',
            filterBy: 'id_carrier',
            filterValue: dataCarriers.myCheapCarrier.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: dataCarriers.myCarrier.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByDelay',
            filterType: 'input',
            filterBy: 'delay',
            filterValue: dataCarriers.clickAndCollect.transitName,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterByStatus',
            filterType: 'select',
            filterBy: 'active',
            filterValue: dataCarriers.clickAndCollect.enable ? '1' : '0',
          },
        expected: 'Enabled',
      },
      {
        args:
          {
            testIdentifier: 'filterByFreeShipping',
            filterType: 'select',
            filterBy: 'is_free',
            filterValue: dataCarriers.myLightCarrier.freeShipping ? '1' : '0',
          },
        expected: 'Disabled',
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boCarriersPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCarriersAfterFilter = await boCarriersPage.getNumberOfElementInGrid(page);
        expect(numberOfCarriersAfterFilter).to.be.at.most(numberOfCarriers);

        for (let row = 1; row <= numberOfCarriersAfterFilter; row++) {
          const textColumn = await boCarriersPage.getTextColumn(page, row, test.args.filterBy);

          if (test.expected !== undefined) {
            expect(textColumn).to.contains(test.expected);
          } else {
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCarriersAfterReset = await boCarriersPage.resetAndGetNumberOfLines(page);
        expect(numberOfCarriersAfterReset).to.equal(numberOfCarriers);
      });
    });
  });

  // 2 - Sort carriers table
  describe('Sort carriers table', async () => {
    [
      {
        testIdentifier: 'sortByIdDesc', sortBy: 'id_carrier', sortDirection: 'desc', isFloat: true,
      },
      {
        testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc',
      },
      {
        testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc',
      },
      {
        testIdentifier: 'sortByPositionAsc', sortBy: 'a!position', sortDirection: 'asc', isFloat: true,
      },
      {
        testIdentifier: 'sortByPositionDesc', sortBy: 'a!position', sortDirection: 'desc', isFloat: true,
      },
      {
        testIdentifier: 'sortByIdAsc', sortBy: 'id_carrier', sortDirection: 'asc', isFloat: true,
      },
    ].forEach((test: {
      testIdentifier: string
      sortBy: string
      sortDirection: string
      isFloat?: boolean
    }) => {
      it(`should sort by '${test.sortBy}' '${test.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.testIdentifier, baseContext);

        const nonSortedTable = await boCarriersPage.getAllRowsColumnContent(page, test.sortBy);

        await boCarriersPage.sortTable(page, test.sortBy, test.sortDirection);

        const sortedTable = await boCarriersPage.getAllRowsColumnContent(page, test.sortBy);

        if (test.isFloat) {
          const nonSortedTableFloat = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

          if (test.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 3 - Create 16 carriers
  describe('Create 16 carriers in BO', async () => {
    const creationTests: number[] = new Array(17).fill(0, 0, 17);
    creationTests.forEach((test: number, index: number) => {
      before(() => utilsFile.generateImage(`todelete${index}.jpg`));

      const carrierData: FakerCarrier = new FakerCarrier({
        name: `todelete${index}`,
        ranges: [
          {
            weightMin: 0,
            weightMax: 5,
            zones: [
              {
                zone: dataZones.europe,
                price: 5,
              },
            ],
          },
        ],
      });

      it('should go to add new carrier page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddCarrierPage${index}`, baseContext);

        await boCarriersPage.goToAddNewCarrierPage(page);

        const pageTitle = await boCarriersCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boCarriersCreatePage.pageTitleCreate);
      });

      it(`should create carrier n°${index + 1} and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createCarrier${index}`, baseContext);

        const textResult = await boCarriersCreatePage.createEditCarrier(page, carrierData);
        expect(textResult).to.contains(boCarriersPage.successfulCreationMessage);
      });

      it('should return to carriers page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `returnToCarriers${index}`, baseContext);

        await boDashboardPage.goToSubMenu(
          page,
          boDashboardPage.shippingLink,
          boDashboardPage.carriersLink,
        );

        const pageTitle = await boCarriersPage.getPageTitle(page);
        expect(pageTitle).to.contains(boCarriersPage.pageTitle);

        const numberOfCarriersAfterCreation = await boCarriersPage.getNumberOfElementInGrid(page);
        expect(numberOfCarriersAfterCreation).to.be.equal(numberOfCarriers + 1 + index);
      });

      after(() => utilsFile.deleteFile(`todelete${index}.jpg`));
    });
  });

  // 4 - Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await boCarriersPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boCarriersPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boCarriersPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await boCarriersPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 5 : Delete carriers created with bulk actions
  describe('Delete carriers with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boCarriersPage.filterTable(
        page,
        'input',
        'name',
        'todelete',
      );

      const numberOfCarriersAfterFilter = await boCarriersPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfCarriersAfterFilter; i++) {
        const textColumn = await boCarriersPage.getTextColumn(
          page,
          i,
          'name',
        );
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete carriers with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteCarriers', baseContext);

      const deleteTextResult = await boCarriersPage.bulkDeleteCarriers(page);
      expect(deleteTextResult).to.be.contains(boCarriersPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfCarriersAfterReset = await boCarriersPage.resetAndGetNumberOfLines(page);
      expect(numberOfCarriersAfterReset).to.be.equal(numberOfCarriers);
    });
  });
});
