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
const addCartRulePage = require('@pages/BO/catalog/discounts/add');

// Import demo data
const {DefaultCustomer} = require('@data/demo/customer');
const {Products} = require('@data/demo/products');
const {Carriers} = require('@data/demo/carriers');

// Import faker data
const CartRuleFaker = require('@data/faker/cartRule');

// Import common tests
const loginCommon = require('@commonTests/BO/loginBO');
const {
  createCartRuleTest,
  deleteCartRuleTest,
  bulkDeleteCartRulesTest,
} = require('@commonTests/BO/catalog/createDeleteCartRule');

const baseContext = 'functional_BO_orders_orders_createOrders_searchAddRemoveVoucher';

let browserContext;
let page;
let addVoucherPage;

// Data to create cart rule without code
const cartRuleWithoutCodeData = new CartRuleFaker(
  {
    name: 'Without code',
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
    name: 'With code',
    code: 'Discount',
    discountType: 'Amount',
    discountAmount: {
      value: 8,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
  },
);

// Data to create disabled cart rule from add order page
const disabledCartRuleData = new CartRuleFaker({
  name: 'Disabled',
  status: false,
  discountType: 'Percent',
  discountPercent: 20,
});

// Data to create cart rule with gift
const cartRuleWithGiftData = new CartRuleFaker(
  {
    name: 'With gift',
    code: 'gift',
    freeGift: true,
    freeGiftProduct: Products.demo_13,
  },
);

// Data to create cart rule with Free shipping
const cartRuleFreeShippingData = new CartRuleFaker({name: 'Free shipping', code: 'free', freeShipping: true});
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
  describe('Add product to cart then check voucher and summary blocks', async () => {
    describe('Check cart rule without code', async () => {
      it('should add to cart the product \'demo_12\' and check details', async function () {
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

      it('should check the voucher details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkVouchersBlock', baseContext);

        const result = await addOrderPage.getVoucherDetailsFromTable(page);
        await Promise.all([
          expect(result.name).to.contains(cartRuleWithoutCodeData.name),
          expect(result.description).to.equal(cartRuleWithoutCodeData.name),
          expect(result.value).to.equal(cartRuleWithoutCodeData.discountAmount.value),
        ]);

        await page.screenshot({path: './screenshots/searchVoucher.png', fullPage: true});
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

      it('should try to remove voucher and check that the voucher is not removed', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher', baseContext);

        await addOrderPage.removeVoucher(page, 1);

        const result = await addOrderPage.getVoucherDetailsFromTable(page);
        await expect(result.name).to.contains(cartRuleWithoutCodeData.name);
      });
    });

    // Post-condition: Delete created cart rule without code
    deleteCartRuleTest(cartRuleWithoutCodeData.name, baseContext);

    describe('Check cart rule with code', async () => {
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

      it('should add to cart the product \'demo_12\' and check details', async function () {
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

      it('should check that the vouchers table is not visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkThatVouchersBlockIsEmpty', baseContext);

        const isVoucherTableNotVisible = await addOrderPage.isVouchersTableNotVisible(page);
        await expect(isVoucherTableNotVisible).to.be.true;
      });

      it('should search for the created voucher with code and check details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher', baseContext);

        const voucherToSelect = await addOrderPage.searchVoucher(page, cartRuleWithCodeData.name);
        await expect(voucherToSelect).to.equal(`${cartRuleWithCodeData.name} - ${cartRuleWithCodeData.code}`);

        await page.screenshot({path: './screenshots/searchVoucherWithCode.png', fullPage: true});

        const result = await addOrderPage.getVoucherDetailsFromTable(page);
        await Promise.all([
          expect(result.name).to.contains(cartRuleWithCodeData.name),
          expect(result.description).to.equal(cartRuleWithCodeData.name),
          expect(result.value).to.equal(cartRuleWithCodeData.discountAmount.value),
        ]);
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock', baseContext);

        const totalTaxes = await basicHelper.percentage(
          Products.demo_12.price_ht - cartRuleWithCodeData.discountAmount.value,
          20,
        );
        const totalTaxExcluded = Products.demo_12.price_ht - cartRuleWithCodeData.discountAmount.value;
        const totalTaxIncluded = totalTaxes + totalTaxExcluded;

        const result = await addOrderPage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
          expect(result.totalVouchers).to.equal(`-€${cartRuleWithCodeData.discountAmount.value.toFixed(2)}`),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${totalTaxExcluded.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxIncluded.toFixed(2)}`),
        ]);
      });

      it('should search for the same created voucher and check the error message', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchExistingVoucher', baseContext);

        await addOrderPage.searchVoucher(page, cartRuleWithCodeData.name);

        const voucherErrorText = await addOrderPage.getCartRuleErrorText(page);
        await expect(voucherErrorText).to.equal(addOrderPage.cartRuleErrorText);
      });

      it('should remove voucher and check that vouchers table is not visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeVoucherAndCheckSummary', baseContext);

        await addOrderPage.removeVoucher(page, 1);

        const isVoucherTableNotVisible = await addOrderPage.isVouchersTableNotVisible(page);
        await expect(isVoucherTableNotVisible).to.be.true;
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock', baseContext);

        const totalTaxes = Products.demo_12.price_ttc - Products.demo_12.price_ht;

        const result = await addOrderPage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
          expect(result.totalVouchers).to.equal('€0.00'),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price_ttc.toFixed(2)}`),
        ]);
      });

      it('should search for an invalid voucher', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchInvalidVoucher', baseContext);

        const searchResult = await addOrderPage.searchVoucher(page, 'testVoucher');
        await expect(searchResult).to.equal('No voucher was found');
      });
    });

    describe('Check disabled cart rule', async () => {
      it('should click on add voucher button and check if the iframe is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher', baseContext);

        const isIframeVisible = await addOrderPage.clickOnAddVoucherButton(page);
        await expect(isIframeVisible).to.be.true;

        addVoucherPage = await addOrderPage.getCreateVoucherIframe(page);
      });

      it('should create then search for the disabled voucher and check the error message', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher', baseContext);

        await addCartRulePage.createCartRule(addVoucherPage, disabledCartRuleData);

        await addOrderPage.searchVoucher(page, disabledCartRuleData.name);

        const voucherErrorText = await addOrderPage.getCartRuleErrorText(page);
        await expect(voucherErrorText).to.equal('This voucher is disabled');
      });
    });

    describe('Check cart rule with gift product', async () => {
      it('should click on add voucher button and check if the iframe is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher', baseContext);

        const isIframeVisible = await addOrderPage.clickOnAddVoucherButton(page);
        await expect(isIframeVisible).to.be.true;
      });

      it('should create the cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher', baseContext);

        addVoucherPage = await addOrderPage.getCreateVoucherIframe(page);

        await addCartRulePage.createCartRule(addVoucherPage, cartRuleWithGiftData);
      });

      it('should search for the created voucher and check details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher', baseContext);

        const voucherToSelect = await addOrderPage.searchVoucher(page, cartRuleWithGiftData.name);
        await expect(voucherToSelect).to.equal(`${cartRuleWithGiftData.name} - ${cartRuleWithGiftData.code}`);

        const result = await addOrderPage.getVoucherDetailsFromTable(page, 1);
        await Promise.all([
          expect(result.name).to.contains(cartRuleWithGiftData.name),
          expect(result.description).to.equal(cartRuleWithGiftData.name),
          expect(result.value).to.equal(Products.demo_12.price_ht),
        ]);
      });

      it('should check that the gift product is added successfully', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher', baseContext);

        const result = await addOrderPage.getProductGiftDetailsFromTable(page, 2);
        await Promise.all([
          expect(result.image).to.contains(Products.demo_13.coverImage),
          expect(result.description).to.equal(Products.demo_13.name),
          expect(result.reference).to.equal(Products.demo_13.reference),
          expect(result.basePrice).to.equal('Gift'),
          expect(result.quantity).to.equal(1),
          expect(result.price).to.equal('Gift'),
        ]);
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock', baseContext);

        const totalTaxes = Products.demo_12.price_ttc - Products.demo_12.price_ht;

        const result = await addOrderPage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
          expect(result.totalVouchers).to.equal('€0.00'),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price_ttc.toFixed(2)}`),
        ]);
      });

      it('should remove voucher and check that the voucher is deleted successfully from the table', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher', baseContext);

        await addOrderPage.removeVoucher(page, 1);

        const isVoucherTableNotVisible = await addOrderPage.isVouchersTableNotVisible(page);
        await expect(isVoucherTableNotVisible).to.be.true;
      });

      it('should check that the gift product is deleted', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher', baseContext);

        const isRowNotVisible = await addOrderPage.isProductTableRowNotVisible(page, 2);
        await expect(isRowNotVisible).to.be.true;
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock', baseContext);

        const totalTaxes = Products.demo_12.price_ttc - Products.demo_12.price_ht;

        const result = await addOrderPage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
          expect(result.totalVouchers).to.equal('€0.00'),
          expect(result.totalShipping).to.equal('€0.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price_ttc.toFixed(2)}`),
        ]);
      });
    });

    describe('Check cart rule Free shipping', async () => {
      it('should click on add voucher button and check if the iframe is visible', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher', baseContext);

        const isIframeVisible = await addOrderPage.clickOnAddVoucherButton(page);
        await expect(isIframeVisible).to.be.true;
      });

      it('should create the cart rule', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher', baseContext);

        addVoucherPage = await addOrderPage.getCreateVoucherIframe(page);

        await addCartRulePage.createCartRule(addVoucherPage, cartRuleFreeShippingData);
      });

      it('should search for the created voucher and check details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher', baseContext);

        const voucherToSelect = await addOrderPage.searchVoucher(page, cartRuleFreeShippingData.name);
        await expect(voucherToSelect).to.equal(`${cartRuleFreeShippingData.name} - ${cartRuleFreeShippingData.code}`);

        const result = await addOrderPage.getVoucherDetailsFromTable(page);
        await Promise.all([
          expect(result.name).to.contains(cartRuleFreeShippingData.name),
          expect(result.description).to.equal(cartRuleFreeShippingData.name),
          expect(result.value).to.equal(0),
        ]);
      });

      it('should select the carrier myCarrier', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher', baseContext);

        const shippingPriceTTC = await addOrderPage.setDeliveryOption(
          page, `${Carriers.myCarrier.name} - Delivery next day!`,
        );
        await expect(shippingPriceTTC).to.equal(`€${Carriers.myCarrier.priceTTC.toFixed(2)}`);
      });

      it('should re-check voucher details', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher', baseContext);

        const result = await addOrderPage.getVoucherDetailsFromTable(page);
        await Promise.all([
          expect(result.name).to.contains(cartRuleFreeShippingData.name),
          expect(result.description).to.equal(cartRuleFreeShippingData.name),
          expect(result.value).to.equal(7),
        ]);
      });

      it('should check summary block', async function () {
        await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock', baseContext);

        const totalTaxes = Products.demo_12.price_ttc - Products.demo_12.price_ht;

        const result = await addOrderPage.getSummaryDetails(page);
        await Promise.all([
          expect(result.totalProducts).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
          expect(result.totalVouchers).to.equal('-€7.00'),
          expect(result.totalShipping).to.equal('€7.00'),
          expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
          expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.price_ht.toFixed(2)}`),
          expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price_ttc.toFixed(2)}`),
        ]);
      });
    });
  });

  // Post-condition: Delete created cart rules
  bulkDeleteCartRulesTest(baseContext);
});
