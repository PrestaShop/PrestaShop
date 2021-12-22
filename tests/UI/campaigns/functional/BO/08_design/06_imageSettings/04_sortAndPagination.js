require('module-alias/register');

// Import expect from chai
const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const testContext = require('@utils/testContext');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const imageSettingsPage = require('@pages/BO/design/imageSettings');
const addImageTypePage = require('@pages/BO/design/imageSettings/add');

// Import data
const ImageTypeFaker = require('@data/faker/imageType');

const baseContext = 'functional_BO_design_imageSettings_sortAndPagination';

let browserContext;
let page;
let numberOfImageTypes = 0;

/*
Create 15 image settings
Paginate between pages
Sort image settings table by ID, Name, Width and Height
Delete image settings with bulk actions
 */
describe('BO - Design - Image Settings : Pagination and sort image settings', async () => {
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

  it('should go to \'Design > Image Settings\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToImageSettingsPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.designParentLink,
      dashboardPage.imageSettingsLink,
    );

    await imageSettingsPage.closeSfToolBar(page);

    const pageTitle = await imageSettingsPage.getPageTitle(page);
    await expect(pageTitle).to.contains(imageSettingsPage.pageTitle);
  });

  it('should reset all filters and get number of image types in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfImageTypes = await imageSettingsPage.resetAndGetNumberOfLines(page);
    await expect(numberOfImageTypes).to.be.above(0);
  });

  // 1 : Create 15 new image types
  describe('Create 15 image types', async () => {
    const creationTests = new Array(15).fill(0, 0, 15);
    creationTests.forEach((test, index) => {
      const createImageTypeData = new ImageTypeFaker({name: `todelete${index}`});

      it('should go to add new image type page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddImageTypePage${index}`, baseContext);

        await imageSettingsPage.goToNewImageTypePage(page);
        const pageTitle = await addImageTypePage.getPageTitle(page);
        await expect(pageTitle).to.contains(addImageTypePage.pageTitleCreate);
      });

      it(`should create image type n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createImageType${index}`, baseContext);

        const textResult = await addImageTypePage.createEditImageType(page, createImageTypeData);
        await expect(textResult).to.contains(imageSettingsPage.successfulCreationMessage);

        const numberOfImageTypesAfterCreation = await imageSettingsPage.getNumberOfElementInGrid(page);
        await expect(numberOfImageTypesAfterCreation).to.be.equal(numberOfImageTypes + 1 + index);
      });
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await imageSettingsPage.selectPaginationLimit(page, '20');
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await imageSettingsPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await imageSettingsPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await imageSettingsPage.selectPaginationLimit(page, '50');
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 : Sort image settings
  describe('Sort image settings table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_image_type', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByWidthAsc', sortBy: 'width', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByWidthDesc', sortBy: 'width', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByHeightAsc', sortBy: 'height', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByHeightDesc', sortBy: 'height', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_image_type', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        let nonSortedTable = await imageSettingsPage.getAllRowsColumnContent(page, test.args.sortBy);

        await imageSettingsPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        let sortedTable = await imageSettingsPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          nonSortedTable = await nonSortedTable.map(text => parseFloat(text));
          sortedTable = await sortedTable.map(text => parseFloat(text));
        }

        const expectedResult = await imageSettingsPage.sortArray(nonSortedTable, test.args.isFloat);

        if (test.args.sortDirection === 'up') {
          await expect(sortedTable).to.deep.equal(expectedResult);
        } else {
          await expect(sortedTable).to.deep.equal(expectedResult.reverse());
        }
      });
    });
  });

  // 4 : Delete image types created with bulk actions
  describe('Delete image types with Bulk Actions', async () => {
    it('should filter list by name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterForBulkDelete', baseContext);

      await imageSettingsPage.filterTable(page, 'input', 'name', 'todelete');

      const numberOfImageTypesAfterFilter = await imageSettingsPage.getNumberOfElementInGrid(page);

      for (let i = 1; i <= numberOfImageTypesAfterFilter; i++) {
        const textColumn = await imageSettingsPage.getTextColumn(page, i, 'name');
        await expect(textColumn).to.contains('todelete');
      }
    });

    it('should delete image types with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteImageTypes', baseContext);

      const deleteTextResult = await imageSettingsPage.bulkDeleteImageTypes(page);
      await expect(deleteTextResult).to.be.contains(imageSettingsPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfImageTypesAfterReset = await imageSettingsPage.resetAndGetNumberOfLines(page);
      await expect(numberOfImageTypesAfterReset).to.be.equal(numberOfImageTypes);
    });
  });
});
