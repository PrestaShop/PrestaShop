require('module-alias/register');

const {expect} = require('chai');

// Helpers to open and close browser
const helper = require('@utils/helpers');

// Common tests login BO
const loginCommon = require('@commonTests/loginBO');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const taxesPage = require('@pages/BO/international/taxes');
const productsPage = require('@pages/BO/catalog/products');
const addProductPage = require('@pages/BO/catalog/products/add');
const ordersPage = require('@pages/BO/orders');
const viewOrderPage = require('@pages/BO/orders/view');

// Import FO pages

// Import faker data
const ProductFaker = require('@data/faker/product');

// Import test context
const testContext = require('@utils/testContext');

const baseContext = 'functional_BO_orders_orders_viewAndEditOrder_checkInvoice';

let browserContext;
let page;

const virtualProduct = new ProductFaker({
  name: 'Virtual product',
  type: 'Virtual product',
  taxRule: 'No tax',
  quantity: 20,
});

const customizedProduct = new ProductFaker({
  name: 'Customized product',
  type: 'Standard product',
  customization: {
    label: 'Type your text here',
    type: 'Text',
    required: true,
  },
});

const productWithSpecificPrice = new ProductFaker({
  name: 'Product with specific price',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  specificPrice: {
    discount: 50,
    startingAt: 2,
    reductionType: '%',
  },
});

const productWithEcoTax = new ProductFaker({
  name: 'Product with ecotax',
  type: 'Standard product',
  taxRule: 'No tax',
  quantity: 20,
  minimumQuantity: 1,
  ecoTax: 10,
});

let numberOfProducts = 0;

/*
Pre-Conditions:
- Create virtual product
- Create customized product
- Create product with specific price
- Create product with ecotax
Scenario:

Post-condition
- Delete virtual product
- Delete customized product
- Delete product with specific price
- Delete product with ecotax

*/
describe('BO - Orders - View and edit order : Check invoice', async () => {

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

  // Pre-condition : Enable ecoTax
  describe('POST-TEST: Enable ecoTax', async () => {
    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.internationalParentLink, dashboardPage.taxesLink);

      await taxesPage.closeSfToolBar(page);

      const pageTitle = await taxesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should enable EcoTax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'enableEcoTax', baseContext);

      const textResult = await taxesPage.enableEcoTax(page, true);
      await expect(textResult).to.be.equal('Update successful');
    });
  });

  // Pre-condition - Create 4 products
  [virtualProduct,
    customizedProduct,
    productWithSpecificPrice,
    productWithEcoTax,
  ].forEach((product, index) => {
    describe(`POST-TEST: Create product '${product.name}'`, async () => {
      if (index === 0) {
        it('should go to \'Catalog > Products\' page', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPage', baseContext);

          await dashboardPage.goToSubMenu(page, dashboardPage.catalogParentLink, dashboardPage.productsLink);

          await productsPage.closeSfToolBar(page);

          const pageTitle = await productsPage.getPageTitle(page);
          await expect(pageTitle).to.contains(productsPage.pageTitle);
        });

        it('should reset all filters and get number of products', async function () {
          await testContext.addContextItem(this, 'testIdentifier', 'resetFiltersBeforeCreate', baseContext);

          numberOfProducts = await productsPage.resetAndGetNumberOfLines(page);
          await expect(numberOfProducts).to.be.above(0);
        });
      }

      it('should go to add product page', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `goToAddProductPage${index}`, baseContext);

        if (index === 0) {
          await productsPage.goToAddProductPage(page);
        } else {
          await addProductPage.goToAddProductPage(page);
        }

        const pageTitle = await addProductPage.getPageTitle(page);
        await expect(pageTitle).to.contains(addProductPage.pageTitle);
      });

      it('should create Product', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `createProduct${index}`, baseContext);

        const createProductMessage = await addProductPage.createEditBasicProduct(page, product);

        if (product === customizedProduct) {
          await addProductPage.addCustomization(page, product.customization);
        }

        if (product === productWithSpecificPrice) {
          await addProductPage.addSpecificPrices(page, product.specificPrice);
        }
        if (product === productWithEcoTax) {
          await addProductPage.addEcoTax(page, product.ecoTax);
        }
        await expect(createProductMessage).to.equal(addProductPage.settingUpdatedMessage);
      });
    });
  });

  // Post-condition - Delete the created products
  describe('Post-condition : Delete the created products', async () => {
    it('should go to \'Catalog > Products\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToProductsPageToDelete', baseContext);

      await addProductPage.goToSubMenu(page, addProductPage.catalogParentLink, addProductPage.productsLink);

      const pageTitle = await productsPage.getPageTitle(page);
      await expect(pageTitle).to.contains(productsPage.pageTitle);
    });

    [virtualProduct,
      customizedProduct,
      productWithSpecificPrice,
      productWithEcoTax,
    ].forEach((product, index) => {
      it(`should delete product '${product.name}' from DropDown Menu`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', `deleteProduct${index}`, baseContext);

        const deleteTextResult = await productsPage.deleteProduct(page, product);
        await expect(deleteTextResult).to.equal(productsPage.productDeletedSuccessfulMessage);
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `resetFiltersAfterDelete${index}`, baseContext);

        const numberOfProductsAfterReset = await productsPage.resetAndGetNumberOfLines(page);
        await expect(numberOfProductsAfterReset).to.be.equal(numberOfProducts + 3 - index);
      });
    });
  });

  // Post-condition - Disable EcoTax
  describe('Disable Eco tax', async () => {
    it('should go to \'International > Taxes\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToTaxesPage2', baseContext);

      await dashboardPage.goToSubMenu(page, dashboardPage.internationalParentLink, dashboardPage.taxesLink);

      await taxesPage.closeSfToolBar(page);

      const pageTitle = await taxesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(taxesPage.pageTitle);
    });

    it('should disable EcoTax', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'disableEcoTax', baseContext);

      const textResult = await taxesPage.enableEcoTax(page, false);
      await expect(textResult).to.be.equal('Update successful');
    });
  });
});
