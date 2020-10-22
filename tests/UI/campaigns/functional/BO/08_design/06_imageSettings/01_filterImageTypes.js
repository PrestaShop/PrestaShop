require('module-alias/register');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import pages
const dashboardPage = require('@pages/BO/dashboard');
const imageSettingsPage = require('@pages/BO/design/imageSettings');

// Import data
const {imageTypes} = require('@data/demo/imageTypes');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_design_imageSettings_filterImageTypes';

// Import expect from chai
const {expect} = require('chai');

// Browser and tab
let browserContext;
let page;

let numberOfImageTypes = 0;

describe('Filter image types by id, name, width, height and status in pages', async () => {
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

  it('should go to image settings page', async function () {
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

  describe('Filter image types', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_image_type',
            filterValue: imageTypes.first.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: imageTypes.first.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterWidth',
            filterType: 'input',
            filterBy: 'width',
            filterValue: imageTypes.first.width,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterHeight',
            filterType: 'input',
            filterBy: 'height',
            filterValue: imageTypes.first.height,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterProducts',
            filterType: 'select',
            filterBy: 'products',
            filterValue: imageTypes.first.productsStatus,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterCategories',
            filterType: 'select',
            filterBy: 'categories',
            filterValue: imageTypes.first.categoriesStatus,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterManufacturers',
            filterType: 'select',
            filterBy: 'manufacturers',
            filterValue: imageTypes.first.manufacturersStatus,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterSuppliers',
            filterType: 'select',
            filterBy: 'suppliers',
            filterValue: imageTypes.first.suppliersStatus,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterStores',
            filterType: 'select',
            filterBy: 'stores',
            filterValue: imageTypes.first.storesStatus,
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
        await expect(numberOfImageTypesAfterFilter).to.be.at.most(numberOfImageTypes);

        for (let row = 1; row <= numberOfImageTypesAfterFilter; row++) {
          if (typeof test.args.filterValue === 'boolean') {
            const status = await imageSettingsPage.getImageTypeStatus(page, row, test.args.filterBy);
            await expect(status).to.equal(test.args.filterValue);
          } else {
            const textColumn = await imageSettingsPage.getTextColumn(
              page,
              row,
              test.args.filterBy,
            );

            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfImageTypesAfterReset = await imageSettingsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfImageTypesAfterReset).to.equal(numberOfImageTypes);
      });
    });
  });
});
