// Import utils
import basicHelper from '@utils/basicHelper';
import date from '@utils/date';
import helper from '@utils/helpers';
import testContext from '@utils/testContext';

// Import common tests
import {createCartRuleTest, bulkDeleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import loginCommon from '@commonTests/BO/loginBO';

// Import BO pages
import cartRulesPage from '@pages/BO/catalog/discounts';
import addCartRulePage from '@pages/BO/catalog/discounts/add';
import dashboardPage from '@pages/BO/dashboard';
import ordersPage from '@pages/BO/orders';
import addOrderPage from '@pages/BO/orders/add';

// Import data
import Carriers from '@data/demo/carriers';
import Customers from '@data/demo/customers';
import Products from '@data/demo/products';
import CartRuleData from '@data/faker/cartRule';

import {expect} from 'chai';
import type {BrowserContext, Page} from 'playwright';
import {Frame} from 'playwright';

const baseContext: string = 'functional_BO_orders_orders_createOrders_searchAddRemoveVoucher';

/*
Pre-condition:
- Create carte rule without code
- Create cart rule with code
Scenario:
- Go to BO > create order page
- Add product to cart the check the existence of voucher without code
- Add voucher with code and check it
- Search the same voucher with code and check error message
- Search for an invalid voucher and check error message
- Search for disabled voucher and check error message
- Add cart rule with gift product and check it
- Add cart rule with free shipping and change the carrier and check it
Post-condition:
- Delete created cart rules
 */
describe('BO - Orders - Create order : Search, add and remove voucher', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let numberOfCartRules: number = 0;
  let addVoucherPage: Frame|null;

  const pastDate: string = date.getDateFormat('yyyy-mm-dd', 'past');
  // Data to create cart rule without code
  const cartRuleWithoutCodeData: CartRuleData = new CartRuleData({
    name: 'WithoutCode',
    dateFrom: pastDate,
    discountType: 'Amount',
    discountAmount: {
      value: 10,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
  });
  // Data to create cart rule with code
  const cartRuleWithCodeData: CartRuleData = new CartRuleData({
    name: 'WithCode',
    code: 'Discount',
    discountType: 'Amount',
    discountAmount: {
      value: 8,
      currency: 'EUR',
      tax: 'Tax excluded',
    },
  });
  // Data to create disabled cart rule from add order page
  const disabledCartRuleData: CartRuleData = new CartRuleData({
    name: 'Disabled',
    status: false,
    discountType: 'Percent',
    discountPercent: 20,
  });
  // Data to create cart rule with gift
  const cartRuleWithGiftData: CartRuleData = new CartRuleData({
    name: 'WithGift',
    code: 'gift',
    freeGift: true,
    freeGiftProduct: Products.demo_13,
  });
  // Data to create cart rule with Free shipping
  const cartRuleFreeShippingData: CartRuleData = new CartRuleData({name: 'FreeShipping', code: 'free', freeShipping: true});

  // Pre-condition: Create cart rule without code
  createCartRuleTest(cartRuleWithoutCodeData, `${baseContext}_preTest_1`);

  // Pre-condition: Create cart rule with code
  createCartRuleTest(cartRuleWithCodeData, `${baseContext}_preTest_2`);

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

    it(`should choose customer ${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await addOrderPage.searchCustomer(page, Customers.johnDoe.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      await expect(isCartsTableVisible, 'History block is not visible!').to.be.true;
    });
  });

  // 2 - Check cart rule without code
  describe('Check cart rule without code', async () => {
    it('should add to cart the product \'demo_12\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      const productToSelect = `${Products.demo_12.name} - €${Products.demo_12.priceTaxExcluded.toFixed(2)}`;
      await addOrderPage.addProductToCart(page, Products.demo_12, productToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_12.thumbImage),
        expect(result.description).to.equal(Products.demo_12.name),
        expect(result.reference).to.equal(Products.demo_12.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.price).to.equal(Products.demo_12.priceTaxExcluded),
      ]);
    });

    it('should check the voucher details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVouchersBlock1', baseContext);

      const result = await addOrderPage.getVoucherDetailsFromTable(page);
      await Promise.all([
        expect(result.name).to.contains(cartRuleWithoutCodeData.name),
        expect(result.description).to.equal(cartRuleWithoutCodeData.description),
        expect(result.value).to.equal(cartRuleWithoutCodeData.discountAmount!.value),
      ]);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock1', baseContext);

      const totalTaxes = await basicHelper.percentage(
        Products.demo_12.priceTaxExcluded - cartRuleWithoutCodeData.discountAmount!.value,
        20,
      );
      const totalTaxExcluded = Products.demo_12.priceTaxExcluded - cartRuleWithoutCodeData.discountAmount!.value;
      const totalTaxIncluded = totalTaxes + totalTaxExcluded;

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal(`-€${cartRuleWithoutCodeData.discountAmount!.value.toFixed(2)}`),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${totalTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxIncluded.toFixed(2)}`),
      ]);
    });

    it('should try to remove voucher and check that the voucher is not removed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher1', baseContext);

      await addOrderPage.removeVoucher(page, 1);

      const result = await addOrderPage.getVoucherDetailsFromTable(page);
      await expect(result.name).to.contains(cartRuleWithoutCodeData.name);
    });
  });

  // Post test condition - Delete cart rule without code
  describe('Delete cart rule without code', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await dashboardPage.goToSubMenu(
        page,
        dashboardPage.catalogParentLink,
        dashboardPage.discountsLink,
      );

      const pageTitle = await cartRulesPage.getPageTitle(page);
      await expect(pageTitle).to.contains(cartRulesPage.pageTitle);
    });

    it('should reset and get number of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfCartRules = await cartRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCartRules).to.be.at.least(0);
    });

    it('should delete cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRule', baseContext);

      const validationMessage = await cartRulesPage.deleteCartRule(page, 1, cartRuleWithoutCodeData.name);
      await expect(validationMessage).to.contains(cartRulesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCartRulesAfterDelete = await cartRulesPage.resetAndGetNumberOfLines(page);
      await expect(numberOfCartRulesAfterDelete).to.equal(numberOfCartRules - 1);
    });
  });

  // 3 - Check cart rule with code
  describe('Check cart rule with code', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage1', baseContext);

      await ordersPage.goToCreateOrderPage(page);

      const pageTitle = await addOrderPage.getPageTitle(page);
      await expect(pageTitle).to.contains(addOrderPage.pageTitle);
    });

    it(`should choose customer ${Customers.johnDoe.firstName} ${Customers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer1', baseContext);

      await addOrderPage.searchCustomer(page, Customers.johnDoe.email);

      const isCartsTableVisible = await addOrderPage.chooseCustomer(page);
      await expect(isCartsTableVisible, 'History block is not visible!').to.be.true;
    });

    it('should add to cart the product \'demo_12\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      const productToSelect = `${Products.demo_12.name} - €${Products.demo_12.priceTaxExcluded.toFixed(2)}`;
      await addOrderPage.addProductToCart(page, Products.demo_12, productToSelect);

      const result = await addOrderPage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(Products.demo_12.thumbImage),
        expect(result.description).to.equal(Products.demo_12.name),
        expect(result.reference).to.equal(Products.demo_12.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.price).to.equal(Products.demo_12.priceTaxExcluded),
      ]);
    });

    it('should check that the vouchers table is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatVouchersBlockIsNotVisible', baseContext);

      const isVoucherTableNotVisible = await addOrderPage.isVouchersTableNotVisible(page);
      await expect(isVoucherTableNotVisible, 'Vouchers table is visible!').to.be.true;
    });

    it('should search for the created voucher with code and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher1', baseContext);

      const voucherToSelect = await addOrderPage.searchVoucher(page, cartRuleWithCodeData.name);
      await expect(voucherToSelect).to.equal(`${cartRuleWithCodeData.name} - ${cartRuleWithCodeData.code}`);

      const result = await addOrderPage.getVoucherDetailsFromTable(page);
      await Promise.all([
        expect(result.name).to.contains(cartRuleWithCodeData.name),
        expect(result.description).to.equal(cartRuleWithCodeData.description),
        expect(result.value).to.equal(cartRuleWithCodeData.discountAmount!.value),
      ]);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock2', baseContext);

      const totalTaxes = await basicHelper.percentage(
        Products.demo_12.priceTaxExcluded - cartRuleWithCodeData.discountAmount!.value,
        20,
      );
      const totalTaxExcluded = Products.demo_12.priceTaxExcluded - cartRuleWithCodeData.discountAmount!.value;
      const totalTaxIncluded = totalTaxes + totalTaxExcluded;

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal(`-€${cartRuleWithCodeData.discountAmount!.value.toFixed(2)}`),
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
      await expect(voucherErrorText).to.equal(addOrderPage.cartRuleAlreadyExistErrorText);
    });

    it('should remove voucher and check that vouchers table is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher2', baseContext);

      await addOrderPage.removeVoucher(page, 1);

      const isVoucherTableNotVisible = await addOrderPage.isVouchersTableNotVisible(page);
      await expect(isVoucherTableNotVisible, 'Vouchers table is visible!').to.be.true;
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock3', baseContext);

      const totalTaxes = Products.demo_12.price - Products.demo_12.priceTaxExcluded;

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal('€0.00'),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price.toFixed(2)}`),
      ]);
    });
  });

  // 4 - Check invalid cart rule
  describe('Check invalid cart rule', async () => {
    it('should search for an invalid voucher and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchInvalidVoucher', baseContext);

      const searchResult = await addOrderPage.searchVoucher(page, 'testVoucher');
      await expect(searchResult).to.equal(addOrderPage.noVoucherFoudErrorMessage);
    });
  });

  // 5 - Check disabled cart rule
  describe('Check disabled cart rule', async () => {
    it('should click on add voucher button and check if the iframe is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addVoucherAndGetIframe', baseContext);

      const isIframeVisible = await addOrderPage.clickOnAddVoucherButton(page);
      await expect(isIframeVisible, 'Add cart rule frame is not visible!').to.be.true;

      addVoucherPage = await addOrderPage.getCreateVoucherIframe(page);
      await expect(addVoucherPage).to.be.not.null;
    });

    it('should create then search for the disabled voucher and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchDisabledVoucher', baseContext);

      await addCartRulePage.createEditCartRules(addVoucherPage!, disabledCartRuleData, false);

      await addOrderPage.searchVoucher(page, disabledCartRuleData.name);

      const voucherErrorText = await addOrderPage.getCartRuleErrorText(page);
      await expect(voucherErrorText).to.equal(addOrderPage.voucherDisabledErrorMessage);
    });
  });

  // 6 - Check cart rule with gift
  describe('Check cart rule with gift product', async () => {
    it('should click on add voucher button and check if the iframe is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'AddVoucherAndCheckIframe', baseContext);

      const isIframeVisible = await addOrderPage.clickOnAddVoucherButton(page);
      await expect(isIframeVisible, 'Add cart rule frame is not visible!').to.be.true;
    });

    it('should create the cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CreateCArtRuleWithGift', baseContext);

      addVoucherPage = await addOrderPage.getCreateVoucherIframe(page);
      await expect(addVoucherPage).to.be.not.null;

      await addCartRulePage.createEditCartRules(addVoucherPage!, cartRuleWithGiftData, false);
    });

    it('should search for the created voucher and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVoucherWithGift', baseContext);

      const voucherToSelect = await addOrderPage.searchVoucher(page, cartRuleWithGiftData.name);
      await expect(voucherToSelect).to.equal(`${cartRuleWithGiftData.name} - ${cartRuleWithGiftData.code}`);

      const result = await addOrderPage.getVoucherDetailsFromTable(page, 1);
      await Promise.all([
        expect(result.name).to.contains(cartRuleWithGiftData.name),
        expect(result.description).to.equal(cartRuleWithGiftData.description),
        expect(result.value).to.equal(Products.demo_12.priceTaxExcluded),
      ]);
    });

    it('should check that the gift product is added successfully', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGiftProduct', baseContext);

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
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock4', baseContext);

      const totalTaxes = Products.demo_12.price - Products.demo_12.priceTaxExcluded;

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal('€0.00'),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price.toFixed(2)}`),
      ]);
    });

    it('should remove voucher and check that the voucher is deleted successfully from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeVoucherWithGift', baseContext);

      await addOrderPage.removeVoucher(page, 1);

      const isVoucherTableNotVisible = await addOrderPage.isVouchersTableNotVisible(page);
      await expect(isVoucherTableNotVisible, 'Vouchers table is visible!').to.be.true;
    });

    it('should check that the gift product is deleted', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckGiftDeleted', baseContext);

      const isRowNotVisible = await addOrderPage.isProductTableRowNotVisible(page, 2);
      await expect(isRowNotVisible, 'Gift product still visible on products table!').to.be.true;
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock5', baseContext);

      const totalTaxes = Products.demo_12.price - Products.demo_12.priceTaxExcluded;

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal('€0.00'),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price.toFixed(2)}`),
      ]);
    });
  });

  // 7 - Check cart rule with free shipping
  describe('Check cart rule Free shipping', async () => {
    it('should click on add voucher button and check if the iframe is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickToAddFreeShippingVoucher', baseContext);

      const isIframeVisible = await addOrderPage.clickOnAddVoucherButton(page);
      await expect(isIframeVisible, 'Add cart rule frame is not visible!').to.be.true;
    });

    it('should create the cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFreeShippingVoucher', baseContext);

      addVoucherPage = await addOrderPage.getCreateVoucherIframe(page);
      await expect(addVoucherPage).to.be.not.null;

      await addCartRulePage.createEditCartRules(addVoucherPage!, cartRuleFreeShippingData, false);
    });

    it('should search for the created voucher and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchFreeShippingVoucher', baseContext);

      const voucherToSelect = await addOrderPage.searchVoucher(page, cartRuleFreeShippingData.name);
      await expect(voucherToSelect).to.equal(`${cartRuleFreeShippingData.name} - ${cartRuleFreeShippingData.code}`);

      const result = await addOrderPage.getVoucherDetailsFromTable(page);
      await Promise.all([
        expect(result.name).to.contains(cartRuleFreeShippingData.name),
        expect(result.description).to.equal(cartRuleFreeShippingData.description),
        expect(result.value).to.equal(0),
      ]);
    });

    it('should select the carrier myCarrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectMyCarrier', baseContext);

      const shippingPriceTTC = await addOrderPage.setDeliveryOption(
        page, `${Carriers.myCarrier.name} - Delivery next day!`,
      );
      await expect(shippingPriceTTC).to.equal(`€${Carriers.myCarrier.priceTTC.toFixed(2)}`);
    });

    it('should re-check voucher details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVoucherDetails', baseContext);

      const result = await addOrderPage.getVoucherDetailsFromTable(page);
      await Promise.all([
        expect(result.name).to.contains(cartRuleFreeShippingData.name),
        expect(result.description).to.equal(cartRuleFreeShippingData.description),
        expect(result.value).to.equal(7),
      ]);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock6', baseContext);

      const totalTaxes = Products.demo_12.price - Products.demo_12.priceTaxExcluded;

      const result = await addOrderPage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal('-€7.00'),
        expect(result.totalShipping).to.equal('€7.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${Products.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${Products.demo_12.price.toFixed(2)}`),
      ]);
    });
  });

  // Post-condition: Delete created cart rules
  bulkDeleteCartRuleTest(`${baseContext}_postTest_1`);
});
