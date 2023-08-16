// Import utils
import basicHelper from '@utils/basicHelper';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import pages
import attributesPage from '@pages/BO/catalog/attributes';
import addAttributePage from '@pages/BO/catalog/attributes/addAttribute';
import dashboardPage from '@pages/BO/dashboard';

// Import data
import AttributeData from '@data/faker/attribute';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

const baseContext: string = 'functional_BO_catalog_attributesAndFeatures_attributes_attributes_sortPaginationAndBulkDelete';

/*
Go to Attributes & Features page
Create 17 new attributes
Pagination next and previous
Sort attributes table by ID, Name and Position
Delete the created attributes by bulk actions
 */
describe('BO - Catalog - Attributes & Features : Sort, pagination and bulk delete attributes', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAttributes: number = 0;

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

  it('should go to \'Catalog > Attributes & Features\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToAttributesPage', baseContext);

    await dashboardPage.goToSubMenu(
      page,
      dashboardPage.catalogParentLink,
      dashboardPage.attributesAndFeaturesLink,
    );
    await attributesPage.closeSfToolBar(page);

    const pageTitle = await attributesPage.getPageTitle(page);
    expect(pageTitle).to.contains(attributesPage.pageTitle);
  });

  it('should reset all filters and get number of attributes in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfAttributes = await attributesPage.resetAndGetNumberOfLines(page);
    expect(numberOfAttributes).to.be.above(0);
  });

  // 1 : Create 17 new attributes
  const creationTests: number[] = new Array(17).fill(0, 0, 17);
  describe('Create 17 new attributes in BO', async () => {
    creationTests.forEach((test: number, index: number) => {
      const createAttributeData: AttributeData = new AttributeData({name: `todelete${index}`});
      it('should go to add new attribute page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAttributePage${index}`, baseContext);

        await attributesPage.goToAddAttributePage(page);

        const pageTitle = await addAttributePage.getPageTitle(page);
        expect(pageTitle).to.contains(addAttributePage.createPageTitle);
      });

      it(`should create attribute n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createNewAttribute${index}`, baseContext);

        const textResult = await addAttributePage.addEditAttribute(page, createAttributeData);
        expect(textResult).to.contains(attributesPage.successfulCreationMessage);

        const numberOfAttributesAfterCreation = await attributesPage.getNumberOfElementInGrid(page);
        expect(numberOfAttributesAfterCreation).to.equal(numberOfAttributes + 1 + index);
      });
    });
  });

  // 2 : Pagination
  describe('Pagination next and previous', async () => {
    it('should change the items number to 20 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo20', baseContext);

      const paginationNumber = await attributesPage.selectPaginationLimit(page, 20);
      expect(paginationNumber).to.equal('1');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnNext', baseContext);

      const paginationNumber = await attributesPage.paginationNext(page);
      expect(paginationNumber).to.equal('2');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnPrevious', baseContext);

      const paginationNumber = await attributesPage.paginationPrevious(page);
      expect(paginationNumber).to.equal('1');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'changeItemsNumberTo50', baseContext);

      const paginationNumber = await attributesPage.selectPaginationLimit(page, 50);
      expect(paginationNumber).to.equal('1');
    });
  });

  // 3 : Sort attributes
  describe('Sort attributes table', async () => {
    const sortTests = [
      {
        args: {
          testIdentifier: 'sortByIdDesc', sortBy: 'id_attribute_group', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameAsc', sortBy: 'b!name', sortDirection: 'up',
        },
      },
      {
        args: {
          testIdentifier: 'sortByNameDesc', sortBy: 'b!name', sortDirection: 'down',
        },
      },
      {
        args: {
          testIdentifier: 'sortByPositionAsc', sortBy: 'a!position', sortDirection: 'up', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByPositionDesc', sortBy: 'a!position', sortDirection: 'down', isFloat: true,
        },
      },
      {
        args: {
          testIdentifier: 'sortByIdAsc', sortBy: 'id_attribute_group', sortDirection: 'up', isFloat: true,
        },
      },
    ];

    sortTests.forEach((test) => {
      it(`should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        const nonSortedTable = await attributesPage.getAllRowsColumnContent(page, test.args.sortBy);

        await attributesPage.sortTable(page, test.args.sortBy, test.args.sortDirection);

        const sortedTable = await attributesPage.getAllRowsColumnContent(page, test.args.sortBy);

        if (test.args.isFloat) {
          const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
          const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

          const expectedResult: number[] = await basicHelper.sortArrayNumber(nonSortedTableFloat);

          if (test.args.sortDirection === 'up') {
            expect(sortedTableFloat).to.deep.equal(expectedResult);
          } else {
            expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
          }
        } else {
          const expectedResult: string[] = await basicHelper.sortArray(nonSortedTable);

          if (test.args.sortDirection === 'up') {
            expect(sortedTable).to.deep.equal(expectedResult);
          } else {
            expect(sortedTable).to.deep.equal(expectedResult.reverse());
          }
        }
      });
    });
  });

  // 4 : Delete created attributes by bulk actions
  describe('Bulk delete attributes', async () => {
    it('should filter list of attributes', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToBulkDeleteAttributes', baseContext);

      await attributesPage.filterTable(page, 'b!name', 'todelete');

      const textColumn = await attributesPage.getTextColumn(page, 1, 'b!name');
      expect(textColumn).to.contains('todelete');
    });

    it('should delete attributes with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'bulkDeleteAttributes', baseContext);

      const deleteTextResult = await attributesPage.bulkDeleteAttributes(page);
      expect(deleteTextResult).to.be.contains(attributesPage.successfulMultiDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAfterDelete', baseContext);

      const numberOfAttributesAfterReset = await attributesPage.resetAndGetNumberOfLines(page);
      expect(numberOfAttributesAfterReset).to.be.equal(numberOfAttributes);
    });
  });
});
