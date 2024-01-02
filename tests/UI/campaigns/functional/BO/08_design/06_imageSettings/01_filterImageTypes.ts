// Import utils
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import commonTests
import loginCommon from '@commonTests/BO/loginBO';

// Import BO Pages
import dashboardPage from '@pages/BO/dashboard';
import imageSettingsPage from '@pages/BO/design/imageSettings';

// Import data
import ImageTypes from '@data/demo/imageTypes';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';

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
    expect(pageTitle).to.contains(imageSettingsPage.pageTitle);
  });

  it('should reset all filters and get number of image types in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilterFirst', baseContext);

    numberOfImageTypes = await imageSettingsPage.resetAndGetNumberOfLines(page);
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
            filterValue: ImageTypes.first.id.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: ImageTypes.first.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterWidth',
            filterType: 'input',
            filterBy: 'width',
            filterValue: ImageTypes.first.width.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterHeight',
            filterType: 'input',
            filterBy: 'height',
            filterValue: ImageTypes.first.height.toString(),
          },
      },
      {
        args:
          {
            testIdentifier: 'filterProducts',
            filterType: 'select',
            filterBy: 'products',
            filterValue: ImageTypes.first.productsStatus ? '1' : '0',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCategories',
            filterType: 'select',
            filterBy: 'categories',
            filterValue: ImageTypes.first.categoriesStatus ? '1' : '0',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterManufacturers',
            filterType: 'select',
            filterBy: 'manufacturers',
            filterValue: ImageTypes.first.manufacturersStatus ? '1' : '0',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterSuppliers',
            filterType: 'select',
            filterBy: 'suppliers',
            filterValue: ImageTypes.first.suppliersStatus ? '1' : '0',
          },
      },
      {
        args:
          {
            testIdentifier: 'filterStores',
            filterType: 'select',
            filterBy: 'stores',
            filterValue: ImageTypes.first.storesStatus ? '1' : '0',
          },
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await imageSettingsPage.filterTable(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfImageTypesAfterFilter = await imageSettingsPage.getNumberOfElementInGrid(page);
        expect(numberOfImageTypesAfterFilter).to.be.at.most(numberOfImageTypes);

        for (let row = 1; row <= numberOfImageTypesAfterFilter; row++) {
          if (test.args.filterType === 'select') {
            const status = await imageSettingsPage.getImageTypeStatus(page, row, test.args.filterBy);
            expect(status).to.equal(test.args.filterValue === '1');
          } else {
            const textColumn = await imageSettingsPage.getTextColumn(
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

        const numberOfImageTypesAfterReset = await imageSettingsPage.resetAndGetNumberOfLines(page);
        expect(numberOfImageTypesAfterReset).to.equal(numberOfImageTypes);
      });
    });
  });
});
