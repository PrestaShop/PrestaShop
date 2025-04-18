import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boImageSettingsPage,
  boImageSettingsCreatePage,
  boLoginPage,
  type BrowserContext,
  FakerImageType,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_sortAndPagination';

/*
Create 15 image settings
Paginate between pages
Sort image settings table by ID, Name, Width and Height
Delete image settings with bulk actions
 */
describe('BO - Design - Image Settings : Pagination and sort image settings', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfImageTypes: number = 0;

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

  it('should go to \'Design > Image Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToImageSettingsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.designParentLink,
      boDashboardPage.imageSettingsLink,
    );
    await boImageSettingsPage.closeSfToolBar(page);

    const pageTitle = await boImageSettingsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boImageSettingsPage.pageTitle);
  });

  it('should reset all filters and get number of image types in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfImageTypes = await boImageSettingsPage.resetAndGetNumberOfLines(page);
    expect(numberOfImageTypes).to.be.above(0);
  });

  // 1 : Create 15 new image types
  describe('Create 15 image types', async () => {
    const creationTests: number[] = new Array(15).fill(0, 0, 15);
    creationTests.forEach((test: number, index: number) => {
      const createImageTypeData: FakerImageType = new FakerImageType({name: `todelete${index}`});

      it('should go to add new image type page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddImageTypePage${index}`, baseContext);

        await boImageSettingsPage.goToNewImageTypePage(page);

        const pageTitle = await boImageSettingsCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boImageSettingsCreatePage.pageTitleCreate);
      });

      it(`should create image type n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createImageType${index}`, baseContext);

        const textResult = await boImageSettingsCreatePage.createEditImageType(page, createImageTypeData);
        expect(textResult).to.contains(boImageSettingsPage.successfulCreationMessage);

        const numberOfImageTypesAfterCreation = await boImageSettingsPage.getNumberOfElementInGrid(page);
        expect(numberOfImageTypesAfterCreation).to.be.equal(numberOfImageTypes + 1 + index);
      });
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await boImageSettingsPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await boImageSettingsPage.paginationNext(page);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await boImageSettingsPage.paginationPrevious(page);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await boImageSettingsPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort image settings
  describe('Sort image settings table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_image_type', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'asc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'desc',
        },
      },
      {
        args: {
          testIdentifier: 'sortByWidthAsc', sortBy: 'width', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByWidthDesc', sortBy: 'width', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByHeightAsc', sortBy: 'height', sortDirection: 'asc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByHeightDesc', sortBy: 'height', sortDirection: 'desc', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_image_type', sortDirection: 'asc', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await boImageSettingsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await boImageSettingsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await boImageSettingsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult = await utilsCore.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult = await utilsCore.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'asc') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 4 : Delete image types created with bulk actions
  describe('Delete image types with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await boImageSettingsPage.filterTable(page, 'input', 'name', 'todelete');

      const numberOfImageTypesAfterFilter = await boImageSettingsPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfImageTypesAfterFilter; i++) {
        const textColumn = await boImageSettingsPage.getTextColumn(page, i, 'name');
        expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete image types with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteImageTypes', baseContext);

      const deleteTextResult = await boImageSettingsPage.bulkDeleteImageTypes(page);
      expect(deleteTextResult).to.be.contains(boImageSettingsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfImageTypesAfterReset = await boImageSettingsPage.resetAndGetNumberOfLines(page);
      expect(numberOfImageTypesAfterReset).to.be.equal(numberOfImageTypes);
    });
  });
});
