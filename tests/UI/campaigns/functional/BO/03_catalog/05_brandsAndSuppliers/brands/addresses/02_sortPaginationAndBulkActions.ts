import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boBrandAdressesCreatePage,
  boBrandsPage,
  boDashboardPage,
  boLoginPage,
  type BrowserContext,
  dataBrands,
  FakerBrandAddress,
  type Page,
  utilsCore,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_catalog_brandsAndSuppliers_brands_addresses_sortPaginationAndBulkActions';

/*
Create 11 Addresses
Paginate between pages
Sort Addresses table
Enable/Disable/Delete Addresses by bulk actions
 */
describe('BO - Catalog - Brands & Suppliers : Sort, pagination and bulk actions Addresses table', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfAddresses: number = 0;

  const tableName: string = 'manufacturer_address';
  const brandNames: string[] = [dataBrands.brand_1.name, dataBrands.brand_2.name];

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

  it('should go to \'Catalog > Brands & Suppliers\' page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await boDashboardPage.goToSubMenu(
      page,
      boDashboardPage.catalogParentLink,
      boDashboardPage.brandsAndSuppliersLink,
    );
    await boDashboardPage.closeSfToolBar(page);

    const pageTitle = await boBrandsPage.getPageTitle(page);
    expect(pageTitle).to.contains(boBrandsPage.pageTitle);
  });

  it('should reset all filters and get number of addresses in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterAddressesTable', baseContext);

    numberOfAddresses = await boBrandsPage.resetAndGetNumberOfLines(page, tableName);
    expect(numberOfAddresses).to.be.above(0);
  });

  // 1 : Create 10 new addresses
  const creationAddressTests: number[] = new Array(10).fill(0, 0, 10);
  describe('Create 10 new Addresses in BO', async () => {
    creationAddressTests.forEach((test: number, index: number) => {
      it('should go to add new address page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddNewAddressPage${index}`, baseContext);

        await boBrandsPage.goToAddNewBrandAddressPage(page);

        const pageTitle = await boBrandAdressesCreatePage.getPageTitle(page);
        expect(pageTitle).to.contains(boBrandAdressesCreatePage.pageTitle);
      });

      it(`should create address n°${index + 1}`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createAddress${index}`, baseContext);

        const createAddressBrand: string = brandNames[index % brandNames.length];
        const createAddressData: FakerBrandAddress = new FakerBrandAddress({
          brandName: createAddressBrand,
          city: `todelete${index}`,
        });

        const result = await boBrandAdressesCreatePage.createEditBrandAddress(page, createAddressData);
        expect(result).to.equal(boBrandsPage.successfulCreationMessage);

        const numberOfAddressesAfterCreation = await boBrandsPage.getNumberOfElementInGrid(page, tableName);
        expect(numberOfAddressesAfterCreation).to.be.equal(numberOfAddresses + 1 + index);
      });
    });
  });

  // 2 : Pagination of addresses table
  describe('Pagination next and previous of Addresses table', async () => {
    it('should change the items number to 10 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesChangeItemsNumberTo10', baseContext);

      const paginationNumber = await boBrandsPage.selectPaginationLimit(page, tableName, 10);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should click on next', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesClickOnNext', baseContext);

      const paginationNumber = await boBrandsPage.paginationNext(page, tableName);
      expect(paginationNumber).to.contains('(page 2 / 2)');
    });

    it('should click on previous', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesClickOnPrevious', baseContext);

      const paginationNumber = await boBrandsPage.paginationPrevious(page, tableName);
      expect(paginationNumber).to.contains('(page 1 / 2)');
    });

    it('should change the items number to 50 per page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addressesChangeItemsNumberTo50', baseContext);

      const paginationNumber = await boBrandsPage.selectPaginationLimit(page, tableName, 50);
      expect(paginationNumber).to.contains('(page 1 / 1)');
    });
  });

  // 3 : Sort addresses table
  describe('Sort Addresses', async () => {
    const brandsTests = [
      {
        args:
          {
            testIdentifier: 'sortAddressesByIdAddressDesc', sortBy: 'id_address', sortDirection: 'desc', isFloat: true,
          },
      },
      {args: {testIdentifier: 'sortAddressesByNameAsc', sortBy: 'name', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByNameDesc', sortBy: 'name', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortAddressesByFirstNameAsc', sortBy: 'firstname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByFirstNameDesc', sortBy: 'firstname', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortAddressesByLastNameAsc', sortBy: 'lastname', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByLastNameDesc', sortBy: 'lastname', sortDirection: 'desc'}},
      {
        args:
          {
            testIdentifier: 'sortAddressesByPostCodeAsc', sortBy: 'postcode', sortDirection: 'asc', isFloat: true,
          },
      },
      {
        args:
          {
            testIdentifier: 'sortAddressesByPostCodeDesc', sortBy: 'postcode', sortDirection: 'desc', isFloat: true,
          },
      },
      {args: {testIdentifier: 'sortAddressesByCityAsc', sortBy: 'city', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByCityDesc', sortBy: 'city', sortDirection: 'desc'}},
      {args: {testIdentifier: 'sortAddressesByCountryAsc', sortBy: 'country', sortDirection: 'asc'}},
      {args: {testIdentifier: 'sortAddressesByCountryDesc', sortBy: 'country', sortDirection: 'desc'}},
      {
        args:
          {
            testIdentifier: 'sortAddressesByIdAddressAsc', sortBy: 'id_address', sortDirection: 'asc', isFloat: true,
          },
      },
    ];
    brandsTests.forEach((test) => {
      it(
        `should sort by '${test.args.sortBy}' '${test.args.sortDirection}' and check result`,
        async function () {
          await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

          const nonSortedTable = await boBrandsPage.getAllRowsColumnContentAddressesTable(
            page,
            test.args.sortBy,
          );

          await boBrandsPage.sortTableAddresses(page, test.args.sortBy, test.args.sortDirection);

          const sortedTable = await boBrandsPage.getAllRowsColumnContentAddressesTable(page, test.args.sortBy);

          if (test.args.isFloat) {
            const nonSortedTableFloat: number[] = nonSortedTable.map((text: string): number => parseFloat(text));
            const sortedTableFloat: number[] = sortedTable.map((text: string): number => parseFloat(text));

            const expectedResult: number[] = await utilsCore.sortArrayNumber(nonSortedTableFloat);

            if (test.args.sortDirection === 'asc') {
              expect(sortedTableFloat).to.deep.equal(expectedResult);
            } else {
              expect(sortedTableFloat).to.deep.equal(expectedResult.reverse());
            }
          } else {
            const expectedResult: string[] = await utilsCore.sortArray(nonSortedTable);

            if (test.args.sortDirection === 'asc') {
              expect(sortedTable).to.deep.equal(expectedResult);
            } else {
              expect(sortedTable).to.deep.equal(expectedResult.reverse());
            }
          }
        },
      );
    });
  });

  // 4 : Delete addresses with bulk actions
  describe('Delete created Addresses with bulk actions', async () => {
    it('should filter list by city', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToDeleteAddresses', baseContext);

      await boBrandsPage.filterAddresses(page, 'input', 'city', 'todelete');

      const textColumn = await boBrandsPage.getTextColumnFromTableAddresses(page, 1, 'city');
      expect(textColumn).to.contains('todelete');
    });

    it('should delete with Bulk Actions and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteAddresses', baseContext);

      const deleteTextResult = await boBrandsPage.deleteWithBulkActions(page, tableName);
      expect(deleteTextResult).to.be.equal(boBrandsPage.successfulDeleteMessage);
    });

    it('should reset filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterDeleteAddresses', baseContext);

      const numberOfAddressesAfterReset = await boBrandsPage.resetAndGetNumberOfLines(page, tableName);
      expect(numberOfAddressesAfterReset).to.be.equal(numberOfAddresses);
    });
  });
});
