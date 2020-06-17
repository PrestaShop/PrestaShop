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

const baseContext = 'sanity_productsBO_deleteProductsWithBulkActions';

// importing pages
const LoginPage = require('@pages/BO/login');
const DashboardPage = require('@pages/BO/dashboard');
const ProductsPage = require('@pages/BO/catalog/products');
const AddProductPage = require('@pages/BO/catalog/products/add');
const ProductFaker = require('@data/faker/product');

let browserContext;
let page;
const productToCreate = {
  name: 'product To Delete 1',
  type: 'Standard product',
};
const firstProductData = new ProductFaker(productToCreate);
productToCreate.name = 'product To Delete 2';
const secondProductData = new ProductFaker(productToCreate);

// creating pages objects in a function
const init = async function () {
  return {
    loginPage: new LoginPage(page),
    dashboardPage: new DashboardPage(page),
    productsPage: new ProductsPage(page),
    addProductPage: new AddProductPage(page),
  };
};

// Create 2 Standard products in BO and Delete it with Bulk Actions
describe('Create Standard product in BO and Delete it with Bulk Actions', async () => {
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
    await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage1', baseContext);

    await this.pageObjects.dashboardPage.goToSubMenu(
      this.pageObjects.dashboardPage.catalogParentLink,
      this.pageObjects.dashboardPage.productsLink,
    );

    const pageTitle = await this.pageObjects.productsPage.getPageTitle();
    await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFilters', baseContext);
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });

  const productsData = [firstProductData, secondProductData];
  productsData.forEach((productData, index) => {
    it('should create new product', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `createProduct${index + 1}`, baseContext);
      await this.pageObjects.productsPage.goToAddProductPage();
      const createProductMessage = await this.pageObjects.addProductPage.createEditBasicProduct(productData);
      await expect(createProductMessage).to.equal(this.pageObjects.addProductPage.settingUpdatedMessage);
    });

    it('should go to Products page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', `goToProductsPageAfterCreate${index + 1}`, baseContext);

      await this.pageObjects.addProductPage.goToSubMenu(
        this.pageObjects.addProductPage.catalogParentLink,
        this.pageObjects.addProductPage.productsLink,
      );

      const pageTitle = await this.pageObjects.productsPage.getPageTitle();
      await expect(pageTitle).to.contains(this.pageObjects.productsPage.pageTitle);
    });
  });

  it('should delete products with bulk Actions', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'bulkDelete', baseContext);
    // Filter By reference first
    await this.pageObjects.productsPage.filterProducts('name', 'product To Delete ');
    const deleteTextResult = await this.pageObjects.productsPage.deleteAllProductsWithBulkActions();
    await expect(deleteTextResult).to.equal(this.pageObjects.productsPage.productMultiDeletedSuccessfulMessage);
  });

  it('should reset all filters', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersLast', baseContext);
    await this.pageObjects.productsPage.resetFilterCategory();
    const numberOfProducts = await this.pageObjects.productsPage.resetAndGetNumberOfLines();
    await expect(numberOfProducts).to.be.above(0);
  });
});
