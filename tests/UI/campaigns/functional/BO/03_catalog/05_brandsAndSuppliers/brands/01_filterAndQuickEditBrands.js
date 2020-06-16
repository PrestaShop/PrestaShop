/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');

// Import data
const {demoBrands} = require('@data/demo/brands');

// Import pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const BrandsPage = require('@pages/BO/catalog/brands');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_catalog_brandsAndSuppliers_brands_filterQuickEditBrands';

let browserContext;
let page;
let numberOfBrands = 0;

// Init objects needed
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    brandsPage: new BrandsPage(page),
  };
};

// Filter And Quick Edit brands
describe('Filter And Quick Edit brands', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);

    this.pageObjects = await init();
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // Login into BO and go to brands page
  loginCommon.loginBO();

  // GO to Brands Page
  it('should go to brands page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToBrandsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.brandsAndSuppliersLink,
    );

    await this.pageObjects.brandsPage.closeSfToolBar();

    const pageTitle = await this.pageObjects.brandsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.brandsPage.pageTitle);
  });

  it('should reset all filters and get Number of brands in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

    numberOfBrands = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
    await expect(numberOfBrands).to.be.above(0);
  });

  // 1 : Filter brands
  describe('Filter brands', async () => {
    const tests = [
      {
        args:
          {
            testIdentifier: 'filterId',
            filterType: 'input',
            filterBy: 'id_manufacturer',
            filterValue: demoBrands.first.id,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterName',
            filterType: 'input',
            filterBy: 'name',
            filterValue: demoBrands.first.name,
          },
      },
      {
        args:
          {
            testIdentifier: 'filterActive',
            filterType: 'select',
            filterBy: 'active',
            filterValue: demoBrands.first.enabled,
          },
        expected: 'check',
      },
    ];

    tests.forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        if (test.args.filterBy === 'active') {
          await this.pageObjects.brandsPage.filterBrandsEnabled(test.args.filterValue);
        } else {
          await this.pageObjects.brandsPage.filterBrands(
            test.args.filterType,
            test.args.filterBy,
            test.args.filterValue,
          );
        }

        const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
        await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

        for (let i = 1; i <= numberOfBrandsAfterFilter; i++) {
          const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableBrands(i, test.args.filterBy);

          if (test.expected !== undefined) {
            await expect(textColumn).to.contains(test.expected);
          } else {
            await expect(textColumn).to.contains(test.args.filterValue);
          }
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
        await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
      });
    });
  });

  // 2 : Edit brands in list
  describe('Quick Edit brands', async () => {
    it('should filter by brand name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterToQuickEdit', baseContext);

      await this.pageObjects.brandsPage.filterBrands('input', 'name', demoBrands.first.name);
      const numberOfBrandsAfterFilter = await this.pageObjects.brandsPage.getNumberOfElementInGrid('manufacturer');
      await expect(numberOfBrandsAfterFilter).to.be.at.most(numberOfBrands);

      const textColumn = await this.pageObjects.brandsPage.getTextColumnFromTableBrands(1, 'name');
      await expect(textColumn).to.contains(demoBrands.first.name);
    });

    const tests = [
      {args: {action: 'disable', enabledValue: false}},
      {args: {action: 'enable', enabledValue: true}},
    ];

    tests.forEach((test) => {
      it(`should ${test.args.action} first brand`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.action}Brand`, baseContext);

        const isActionPerformed = await this.pageObjects.brandsPage.updateEnabledValue(1, test.args.enabledValue);

        if (isActionPerformed) {
          const resultMessage = await this.pageObjects.brandsPage.getTextContent(
            this.pageObjects.brandsPage.alertSuccessBlockParagraph,
          );

          await expect(resultMessage).to.contains(this.pageObjects.brandsPage.successfulUpdateStatusMessage);
        }

        const brandsStatus = await this.pageObjects.brandsPage.getToggleColumnValue(1);
        await expect(brandsStatus).to.be.equal(test.args.enabledValue);
      });
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterQuickEdit', baseContext);

      const numberOfBrandsAfterReset = await this.pageObjects.brandsPage.resetAndGetNumberOfLines('manufacturer');
      await expect(numberOfBrandsAfterReset).to.equal(numberOfBrands);
    });
  });
});
