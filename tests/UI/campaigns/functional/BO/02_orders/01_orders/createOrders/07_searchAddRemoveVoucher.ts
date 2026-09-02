import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {createCartRuleTest, bulkDeleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';

import {
  boCartRulesPage,
  boCartRulesCreatePage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersCreatePage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataProducts,
  FakerCartRule,
  type Frame,
  type Page,
  utilsCore,
  utilsDate,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

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

  const pastDate: string = utilsDate.getDateFormat('yyyy-mm-dd', 'past');
  // Data to create cart rule without code
  const cartRuleWithoutCodeData: FakerCartRule = new FakerCartRule({
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
  const cartRuleWithCodeData: FakerCartRule = new FakerCartRule({
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
  const disabledCartRuleData: FakerCartRule = new FakerCartRule({
    name: 'Disabled',
    status: false,
    discountType: 'Percent',
    discountPercent: 20,
  });
  // Data to create cart rule with gift
  const cartRuleWithGiftData: FakerCartRule = new FakerCartRule({
    name: 'WithGift',
    code: 'gift',
    freeGift: true,
    freeGiftProduct: dataProducts.demo_13,
  });
  // Data to create cart rule with Free shipping
  const cartRuleFreeShippingData: FakerCartRule = new FakerCartRule({name: 'FreeShipping', code: 'free', freeShipping: true});

  // Pre-condition: Create cart rule without code
  createCartRuleTest(cartRuleWithoutCodeData, `${baseContext}_preTest_1`);

  // Pre-condition: Create cart rule with code
  createCartRuleTest(cartRuleWithCodeData, `${baseContext}_preTest_2`);

  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  // 1 - Go to create order page
  describe('Go to create order page', async () => {
    it('should login in BO', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

      await boLoginPage.goTo(page, global.BO.URL);
      await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

      const pageTitle = await boDashboardPage.getPageTitle(page);
      expect(pageTitle).to.contains(boDashboardPage.pageTitle);
    });

    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage', baseContext);

      await boOrdersPage.goToCreateOrderPage(page);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
    });

    it(`should choose customer ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer', baseContext);

      await boOrdersCreatePage.searchCustomer(page, dataCustomers.johnDoe.email);

      const isCartsTableVisible = await boOrdersCreatePage.chooseCustomer(page);
      expect(isCartsTableVisible, 'History block is not visible!').to.eq(true);
    });
  });

  // 2 - Check cart rule without code
  describe('Check cart rule without code', async () => {
    it('should add to cart the product \'demo_12\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart1', baseContext);

      const productToSelect = `${dataProducts.demo_12.name} - €${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`;
      await boOrdersCreatePage.addProductToCart(page, dataProducts.demo_12, productToSelect);

      const result = await boOrdersCreatePage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_12.thumbImage),
        expect(result.description).to.equal(dataProducts.demo_12.name),
        expect(result.reference).to.equal(dataProducts.demo_12.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.price).to.equal(dataProducts.demo_12.priceTaxExcluded),
      ]);
    });

    it('should check the voucher details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVouchersBlock1', baseContext);

      const result = await boOrdersCreatePage.getVoucherDetailsFromTable(page);
      await Promise.all([
        expect(result.name).to.contains(cartRuleWithoutCodeData.name),
        expect(result.description).to.equal(cartRuleWithoutCodeData.description),
        expect(result.value).to.equal(cartRuleWithoutCodeData.discountAmount!.value),
      ]);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock1', baseContext);

      const discount: number = parseFloat(cartRuleWithoutCodeData.discountAmount!.value.toString());
      const totalTaxes = utilsCore.percentage(
        dataProducts.demo_12.priceTaxExcluded - discount,
        20,
      );
      const totalTaxExcluded = dataProducts.demo_12.priceTaxExcluded - discount;
      const totalTaxIncluded = totalTaxes + totalTaxExcluded;

      const result = await boOrdersCreatePage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal(`-€${discount.toFixed(2)}`),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${totalTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxIncluded.toFixed(2)}`),
      ]);
    });

    it('should try to remove voucher and check that the voucher is not removed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher1', baseContext);

      await boOrdersCreatePage.removeVoucher(page, 1);

      const result = await boOrdersCreatePage.getVoucherDetailsFromTable(page);
      expect(result.name).to.contains(cartRuleWithoutCodeData.name);
    });
  });

  // Post test condition - Delete cart rule without code
  describe('Delete cart rule without code', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });

    it('should reset and get number of cart rules', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetFilter', baseContext);

      numberOfCartRules = await boCartRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCartRules).to.be.at.least(0);
    });

    it('should delete cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteCartRule', baseContext);

      const validationMessage = await boCartRulesPage.deleteCartRule(page, 1, cartRuleWithoutCodeData.name);
      expect(validationMessage).to.contains(boCartRulesPage.successfulDeleteMessage);
    });

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetAfterBulkDelete', baseContext);

      const numberOfCartRulesAfterDelete = await boCartRulesPage.resetAndGetNumberOfLines(page);
      expect(numberOfCartRulesAfterDelete).to.equal(numberOfCartRules - 1);
    });
  });

  // 3 - Check cart rule with code
  describe('Check cart rule with code', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage1', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it('should go to create order page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToCreateOrderPage1', baseContext);

      await boOrdersPage.goToCreateOrderPage(page);

      const pageTitle = await boOrdersCreatePage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersCreatePage.pageTitle);
    });

    it(`should choose customer ${dataCustomers.johnDoe.firstName} ${dataCustomers.johnDoe.lastName}`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'chooseDefaultCustomer1', baseContext);

      await boOrdersCreatePage.searchCustomer(page, dataCustomers.johnDoe.email);

      const isCartsTableVisible = await boOrdersCreatePage.chooseCustomer(page);
      expect(isCartsTableVisible, 'History block is not visible!').to.eq(true);
    });

    it('should add to cart the product \'demo_12\' and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addProductToCart2', baseContext);

      const productToSelect = `${dataProducts.demo_12.name} - €${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`;
      await boOrdersCreatePage.addProductToCart(page, dataProducts.demo_12, productToSelect);

      const result = await boOrdersCreatePage.getProductDetailsFromTable(page);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_12.thumbImage),
        expect(result.description).to.equal(dataProducts.demo_12.name),
        expect(result.reference).to.equal(dataProducts.demo_12.reference),
        expect(result.quantityMin).to.equal(1),
        expect(result.price).to.equal(dataProducts.demo_12.priceTaxExcluded),
      ]);
    });

    it('should check that the vouchers table is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkThatVouchersBlockIsNotVisible', baseContext);

      const isVoucherTableNotVisible = await boOrdersCreatePage.isVouchersTableNotVisible(page);
      expect(isVoucherTableNotVisible, 'Vouchers table is visible!').to.eq(true);
    });

    it('should search for the created voucher with code and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVoucher1', baseContext);

      const voucherToSelect = await boOrdersCreatePage.searchVoucher(page, cartRuleWithCodeData.name);
      expect(voucherToSelect).to.equal(`${cartRuleWithCodeData.name} - ${cartRuleWithCodeData.code}`);

      const result = await boOrdersCreatePage.getVoucherDetailsFromTable(page);
      await Promise.all([
        expect(result.name).to.contains(cartRuleWithCodeData.name),
        expect(result.description).to.equal(cartRuleWithCodeData.description),
        expect(result.value).to.equal(cartRuleWithCodeData.discountAmount!.value),
      ]);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock2', baseContext);

      const discount: number = parseFloat(cartRuleWithCodeData.discountAmount!.value.toString());
      const totalTaxes = utilsCore.percentage(
        dataProducts.demo_12.priceTaxExcluded - discount,
        20,
      );
      const totalTaxExcluded = dataProducts.demo_12.priceTaxExcluded - discount;
      const totalTaxIncluded = totalTaxes + totalTaxExcluded;

      const result = await boOrdersCreatePage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal(`-€${discount.toFixed(2)}`),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${totalTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${totalTaxIncluded.toFixed(2)}`),
      ]);
    });

    it('should search for the same created voucher and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchExistingVoucher', baseContext);

      await boOrdersCreatePage.searchVoucher(page, cartRuleWithCodeData.name);

      const voucherErrorText = await boOrdersCreatePage.getCartRuleErrorText(page);
      expect(voucherErrorText).to.equal(boOrdersCreatePage.cartRuleAlreadyExistErrorText);
    });

    it('should remove voucher and check that vouchers table is not visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeVoucher2', baseContext);

      await boOrdersCreatePage.removeVoucher(page, 1);

      const isVoucherTableNotVisible = await boOrdersCreatePage.isVouchersTableNotVisible(page);
      expect(isVoucherTableNotVisible, 'Vouchers table is visible!').to.eq(true);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock3', baseContext);

      const totalTaxes = dataProducts.demo_12.price - dataProducts.demo_12.priceTaxExcluded;

      const result = await boOrdersCreatePage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal('€0.00'),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${dataProducts.demo_12.price.toFixed(2)}`),
      ]);
    });
  });

  // 4 - Check invalid cart rule
  describe('Check invalid cart rule', async () => {
    it('should search for an invalid voucher and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchInvalidVoucher', baseContext);

      const searchResult = await boOrdersCreatePage.searchVoucher(page, 'testVoucher');
      expect(searchResult).to.equal(boOrdersCreatePage.noVoucherFoudErrorMessage);
    });
  });

  // 5 - Check disabled cart rule
  describe('Check disabled cart rule', async () => {
    it('should click on add voucher button and check if the iframe is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addVoucherAndGetIframe', baseContext);

      const isIframeVisible = await boOrdersCreatePage.clickOnAddVoucherButton(page);
      expect(isIframeVisible, 'Add cart rule frame is not visible!').to.eq(true);

      addVoucherPage = boOrdersCreatePage.getCreateVoucherIframe(page);
      expect(addVoucherPage).to.not.eq(null);
    });

    it('should create then search for the disabled voucher and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchDisabledVoucher', baseContext);

      await boCartRulesCreatePage.createEditCartRules(addVoucherPage!, disabledCartRuleData, false);

      await boOrdersCreatePage.searchVoucher(page, disabledCartRuleData.name);

      const voucherErrorText = await boOrdersCreatePage.getCartRuleErrorText(page);
      expect(voucherErrorText).to.equal(boOrdersCreatePage.voucherDisabledErrorMessage);
    });
  });

  // 6 - Check cart rule with gift
  describe('Check cart rule with gift product', async () => {
    it('should click on add voucher button and check if the iframe is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'AddVoucherAndCheckIframe', baseContext);

      const isIframeVisible = await boOrdersCreatePage.clickOnAddVoucherButton(page);
      expect(isIframeVisible, 'Add cart rule frame is not visible!').to.eq(true);
    });

    it('should create the cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CreateCArtRuleWithGift', baseContext);

      addVoucherPage = boOrdersCreatePage.getCreateVoucherIframe(page);
      expect(addVoucherPage).to.not.eq(null);

      await boCartRulesCreatePage.createEditCartRules(addVoucherPage!, cartRuleWithGiftData, false);
    });

    it('should search for the created voucher and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchVoucherWithGift', baseContext);

      const voucherToSelect = await boOrdersCreatePage.searchVoucher(page, cartRuleWithGiftData.name);
      expect(voucherToSelect).to.equal(`${cartRuleWithGiftData.name} - ${cartRuleWithGiftData.code}`);

      const result = await boOrdersCreatePage.getVoucherDetailsFromTable(page, 1);
      await Promise.all([
        expect(result.name).to.contains(cartRuleWithGiftData.name),
        expect(result.description).to.equal(cartRuleWithGiftData.description),
        expect(result.value).to.equal(dataProducts.demo_12.priceTaxExcluded),
      ]);
    });

    it('should check that the gift product is added successfully', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkGiftProduct', baseContext);

      const result = await boOrdersCreatePage.getProductGiftDetailsFromTable(page, 2);
      await Promise.all([
        expect(result.image).to.contains(dataProducts.demo_13.coverImage),
        expect(result.description).to.equal(dataProducts.demo_13.name),
        expect(result.reference).to.equal(dataProducts.demo_13.reference),
        expect(result.basePrice).to.equal('Gift'),
        expect(result.quantity).to.equal(1),
        expect(result.price).to.equal('Gift'),
      ]);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock4', baseContext);

      const totalTaxes = dataProducts.demo_12.price - dataProducts.demo_12.priceTaxExcluded;

      const result = await boOrdersCreatePage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal('€0.00'),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${dataProducts.demo_12.price.toFixed(2)}`),
      ]);
    });

    it('should remove voucher and check that the voucher is deleted successfully from the table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'removeVoucherWithGift', baseContext);

      await boOrdersCreatePage.removeVoucher(page, 1);

      const isVoucherTableNotVisible = await boOrdersCreatePage.isVouchersTableNotVisible(page);
      expect(isVoucherTableNotVisible, 'Vouchers table is visible!').to.eq(true);
    });

    it('should check that the gift product is deleted', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'CheckGiftDeleted', baseContext);

      const isRowNotVisible = await boOrdersCreatePage.isProductTableRowNotVisible(page, 2);
      expect(isRowNotVisible, 'Gift product still visible on products table!').to.eq(true);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock5', baseContext);

      const totalTaxes = dataProducts.demo_12.price - dataProducts.demo_12.priceTaxExcluded;

      const result = await boOrdersCreatePage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal('€0.00'),
        expect(result.totalShipping).to.equal('€0.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${dataProducts.demo_12.price.toFixed(2)}`),
      ]);
    });
  });

  // 7 - Check cart rule with free shipping
  describe('Check cart rule Free shipping', async () => {
    it('should click on add voucher button and check if the iframe is visible', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickToAddFreeShippingVoucher', baseContext);

      const isIframeVisible = await boOrdersCreatePage.clickOnAddVoucherButton(page);
      expect(isIframeVisible, 'Add cart rule frame is not visible!').to.eq(true);
    });

    it('should create the cart rule', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addFreeShippingVoucher', baseContext);

      addVoucherPage = boOrdersCreatePage.getCreateVoucherIframe(page);
      expect(addVoucherPage).to.not.eq(null);

      await boCartRulesCreatePage.createEditCartRules(addVoucherPage!, cartRuleFreeShippingData, false);
    });

    it('should search for the created voucher and check details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'searchFreeShippingVoucher', baseContext);

      const voucherToSelect = await boOrdersCreatePage.searchVoucher(page, cartRuleFreeShippingData.name);
      expect(voucherToSelect).to.equal(`${cartRuleFreeShippingData.name} - ${cartRuleFreeShippingData.code}`);

      const result = await boOrdersCreatePage.getVoucherDetailsFromTable(page);
      await Promise.all([
        expect(result.name).to.contains(cartRuleFreeShippingData.name),
        expect(result.description).to.equal(cartRuleFreeShippingData.description),
        expect(result.value).to.equal(0),
      ]);
    });

    it('should select the carrier myCarrier', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'selectMyCarrier', baseContext);

      const shippingPriceTTC = await boOrdersCreatePage.setDeliveryOption(
        page, `${dataCarriers.myCarrier.name} - Delivery next day!`,
      );
      expect(shippingPriceTTC).to.equal(`€${dataCarriers.myCarrier.priceTTC.toFixed(2)}`);
    });

    it('should re-check voucher details', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkVoucherDetails', baseContext);

      const result = await boOrdersCreatePage.getVoucherDetailsFromTable(page);
      await Promise.all([
        expect(result.name).to.contains(cartRuleFreeShippingData.name),
        expect(result.description).to.equal(cartRuleFreeShippingData.description),
        expect(result.value).to.equal(7),
      ]);
    });

    it('should check summary block', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkSummaryBlock6', baseContext);

      const totalTaxes = dataProducts.demo_12.price - dataProducts.demo_12.priceTaxExcluded;

      const result = await boOrdersCreatePage.getSummaryDetails(page);
      await Promise.all([
        expect(result.totalProducts).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalVouchers).to.equal('-€7.00'),
        expect(result.totalShipping).to.equal('€7.00'),
        expect(result.totalTaxes).to.equal(`€${totalTaxes.toFixed(2)}`),
        expect(result.totalTaxExcluded).to.equal(`€${dataProducts.demo_12.priceTaxExcluded.toFixed(2)}`),
        expect(result.totalTaxIncluded).to.equal(`Total (Tax incl.) €${dataProducts.demo_12.price.toFixed(2)}`),
      ]);
    });
  });

  // Post-condition: Delete created cart rules
  bulkDeleteCartRuleTest(`${baseContext}_postTest_1`);
});
