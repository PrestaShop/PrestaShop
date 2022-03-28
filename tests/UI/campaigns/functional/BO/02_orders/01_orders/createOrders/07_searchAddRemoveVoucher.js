require('module-alias/register');

const {expect} = require('chai');

// Import utils
const helper = require('@utils/helpers');
const basicHelper = require('@utils/basicHelper');
const testContext = require('@utils/testContext');

// Import BO pages
const dashboardPage = require('@pages/BO/dashboard');
const ordersPage = require('@pages/BO/orders');
const addOrderPage = require('@pages/BO/orders/add');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {Products} = require('@data/demo/products');

// Import faker data
const CartRuleFaker = require('@data/faker/cartRule');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {createCartRuleTest, bulkDeleteCartRuleTest} = require('@commonTests/BO/catalog/createDeleteCartRule');

const baseContext = 'functional_BO_orders_orders_createOrders_searchAddRemoveVoucher';

let browserContext;
let page;

// Data to create cart rule without code
const cartRuleWithoutCodeData = new CartRuleFaker(
  {
    discountType: 'Amount',
    discountAmount: {
      value: 10,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
  },
);

// Data to create cart rule with code
const cartRuleWithCodeData = new CartRuleFaker(
  {
    code: 'Discount',
    discountType: 'Amount',
    discountAmount: {
      value: 10,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
  },
);
/*
Pre-condition:

Scenario:

Post-condition:


 */
describe('BO - Orders - Create order : Search, add and remove voucher', async () => {
  // Pre-condition: Create cart rule without code
  createCartRuleTest(cartRuleWithoutCodeData, baseContext);

  // Pre-condition: Create cart rule with code
  createCartRuleTest(cartRuleWithCodeData, baseContext);

  before(async function () {
    browserContext = await helper.createBrowserContext(this.browser);
    page = await helper.newTab(browserContext);
  });

  after(async () => {
    await helper.closeBrowserContext(browserContext);
  });

  // 1 - Go to create order page
  describe('Go to create order page', async () => {
    it('should login in BO', async function () {
      await loginCommon.loginBO(this, page);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.ordersParentLink,
        dashboardPage.ordersLink,
      );

      await ordersPage.closeSfToolBar(page);

      const pageTitle = await ordersPage.getPageTitle(page);
      await expect(pageTitle).to.contains(ordersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await ordersPage.goToCreateOrderPage(page);
      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${DefaultCustomer.firstName} ${DefaultCustomer.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await addOrderPage.searchCustomer(page, DefaultCustomer.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      await expect(isCartsTableVisible, 'History block is not visible!').to.be.true;
    });
  });

  // 2 - Add product to cart and check voucher block
  describe('Add product to cart and check voucher and summary blocks', async () => {
    it('should add to cart the product with cart rule \'demo_12\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductWithCartRule', baseContext);

      const productToSelect = `${Products.demo_12.name} - €${Products.demo_12.price_ht.toFixed(2)}`;
      await addOrderPage.addProductToCart(page, Products.demo_12, productToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_12.thumbnailImage),
        expect(result.description).to.equal(Products.demo_12.name),
        expect(result.reference).to.equal(Products.demo_12.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.price).to.equal(Products.demo_12.price_ht),
      ]);
    });

    it('should check the vouchers block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVouchersBlock', baseContext);

      const result = await addOrderPage.getVoucherDetailsFromTable(page);
      await Promise.all([
        expect(result.name).to.contains(cartRuleWithoutCodeData.name),
        expect(result.description).to.equal(cartRuleWithoutCodeData.name),
        expect(result.value).to.equal(cartRuleWithoutCodeData.discountAmount.value),
      ]);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock', baseContext);

      const totalTaxes = await basicHelper.percentage(
        Products.demo_12.price_ht - cartRuleWithoutCodeData.discountAmount.value,
        20,
      );
      const totalTaxExcluded = Products.demo_12.price_ht - cartRuleWithoutCodeData.discountAmount.value;
      const totalTaxIncluded = totalTaxes + totalTaxExcluded;

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
        expect(result.totalVouchers).to.equal(`-€${cartRuleWithoutCodeData.discountAmount.value.toFixed(2)}`),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${totalTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxIncluded.toFixed(2)}`),
      ]);
    });

    it('should remove voucher', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher', baseContext);
    });
  });

  // Post-condition: Delete created cart rules
  bulkDeleteCartRuleTest(baseContext);
});
