import testContext from '@utils/testContext';
import {expect} from 'chai';

import {
  boDashboardPage,
  boImageSettingsPage,
  boLoginPage,
  type BrowserContext,
  dataImageTypes,
  type Page,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_design_imageSettings_filterImageTypes';

/*
Filter image types table by ID, name, Width, Height, Products, Categories, Brands, Suppliers and Stores
 */
describe('BO - Design - Positions : Filter image types table', async () => {
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

  describe('Filter image types table', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_image_type',
            filterValue: dataImageTypes.imageType_1.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: dataImageTypes.imageType_1.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterWidth',
            filterType: 'input',
            filterBy: 'width',
            filterValue: dataImageTypes.imageType_1.width.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterHeight',
            filterType: 'input',
            filterBy: 'height',
            filterValue: dataImageTypes.imageType_1.height.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterProducts',
            filterType: 'select',
            filterBy: 'products',
            filterValue: dataImageTypes.imageType_1.productsStatus ? 'Yes' : 'No',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCategories',
            filterType: 'select',
            filterBy: 'categories',
            filterValue: dataImageTypes.imageType_1.categoriesStatus ? 'Yes' : 'No',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterManufacturers',
            filterType: 'select',
            filterBy: 'manufacturers',
            filterValue: dataImageTypes.imageType_1.manufacturersStatus ? 'Yes' : 'No',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterSuppliers',
            filterType: 'select',
            filterBy: 'suppliers',
            filterValue: dataImageTypes.imageType_1.suppliersStatus ? 'Yes' : 'No',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterStores',
            filterType: 'select',
            filterBy: 'stores',
            filterValue: dataImageTypes.imageType_1.storesStatus ? 'Yes' : 'No',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boImageSettingsPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfImageTypesAfterFilter = await boImageSettingsPage.getNumberOfElementInGrid(page);
        expect(numberOfImageTypesAfterFilter).to.be.at.most(numberOfImageTypes);

        for (let row = 1; row <= numberOfImageTypesAfterFilter; row++) {
          if (test.args.filterType === 'select') {
            const status = await boImageSettingsPage.getImageTypeStatus(page, row, test.args.filterBy);
            expect(status).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await boImageSettingsPage.getTextColumn(
              page,
              row,
              test.args.filterBy,
            );
            expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfImageTypesAfterReset = await boImageSettingsPage.resetAndGetNumberOfLines(page);
        expect(numberOfImageTypesAfterReset).to.equal(numberOfImageTypes);
      });
    });
  });
});
