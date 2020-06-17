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
// Using chai
const {expect} = require('chai');
const helper = require('@utils/helpers');
const loginCommon = require('@commonTests/loginBO');
const testContext = require('@utils/testContext');

const baseContext = 'sanity_productsBO_filterProducts';

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductsPage = require('@pages/BO/catalog/products');
const {Products} = require('@data/demo/products');
const {Categories} = require('@data/demo/categories');
const {DefaultFrTax} = require('@data/demo/tax');

let browserContext;
let page;
let numberOfProducts = 0;

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productsPage: new ProductsPage(page),
  };
};

// Test of filters in products page
describe('Filter in Products Page', async () => {
  // before and after functions
  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
    this.pageObjects = await init();
  });
  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });
  // Steps
  loginCommon.loginBO();

  it('should go to Products page', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.productsLink,
    );

    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters and get Number of products in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);
    await this.pageObjects.productsPage.resetFilterCategory();
    numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);

    // Do not loop more than the products displayed via the pagination
    const numberOfProductsOnPage = await this.pageObjects.productsPage.getNumberOfProductsOnPage();
    // Check that prices have correct tax values
    for (let i = 1; i <= numberOfProducts && i <= numberOfProductsOnPage; i++) {
      const productPrice = await this.pageObjects.productsPage.getProductPriceFromList(i);
      const productPriceTTC = await this.pageObjects.productsPage.getProductPriceFromList(i, true);
      const conversionRate = (100 + parseInt(DefaultFrTax.rate, 10)) / 100;
      await expect(parseFloat(productPrice)).to.equal(parseFloat((productPriceTTC / conversionRate).toFixed(2)));
    }
  });

  const tests = [
    {args: {identifier: 'filterName', filterBy: 'name', filterValue: Products.demo_14.name}},
    {args: {identifier: 'filterReference', filterBy: 'reference', filterValue: Products.demo_1.reference}},
    {args: {identifier: 'filterCategory', filterBy: 'category', filterValue: Categories.men.name}},
  ];
  tests.forEach((test) => {
    it(`should filter list by ${test.args.filterBy} and check result`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', `filterBy_${test.args.identifier}`, baseContext);
      if (test.args.filterBy === 'category') {
        await this.pageObjects.productsPage.filterProductsByCategory(test.args.filterValue);
      } else {
        await this.pageObjects.productsPage.filterProducts(test.args.filterBy, test.args.filterValue);
      }
      const numberOfProductsAfterFilter = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      await expect(numberOfProductsAfterFilter).to.be.below(numberOfProducts);
    });

    it('should reset filter and check result', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `resetFilters_${test.args.identifier}`, baseContext);
      let numberOfProductsAfterReset;
      if (test.args.filterBy === 'category') {
        await this.pageObjects.productsPage.resetFilterCategory();
        numberOfProductsAfterReset = await this.pageObjects.productsPage.getNumberOfProductsFromList();
      } else {
        numberOfProductsAfterReset = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
      }
      await expect(numberOfProductsAfterReset).to.equal(numberOfProducts);
    });
  });
});
