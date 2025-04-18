import testContext from '@utils/testContext';
import {expect} from 'chai';

// Import common tests
import {bulkDeleteCartRuleTest} from '@commonTests/BO/catalog/cartRule';
import {createOrderByCustomerTest} from '@commonTests/FO/classic/order';

import {
  boCartRulesPage,
  boDashboardPage,
  boLoginPage,
  boOrdersPage,
  boOrdersViewBlockProductsPage,
  boOrdersViewBlockTabListPage,
  type BrowserContext,
  dataCarriers,
  dataCustomers,
  dataPaymentMethods,
  dataProducts,
  FakerOrder,
  FakerOrderShipping,
  type Page,
  type ProductDiscount,
  utilsPlaywright,
} from '@prestashop-core/ui-testing';

const baseContext: string = 'functional_BO_orders_orders_viewAndEditOrder_addDiscount';

/*
Pre-condition :
- Create order by default customer
Scenario :
- Create different cart rules :
  - Incorrect percent value (text)
  - Percent value > 100
  - Percent value < 0
  - Good percent value
  - Incorrect amount value (text)
  - Amount value < 0
  - Amount value > total
  - Good amount value
  - Free shipping
- Check discount table, discounts and total ordered on each created cart rule
- Check created cart rules on discount page
Post-condition
- Delete created cart rules
 */

describe('BO - Orders - View and edit order : Add discount', async () => {
  let browserContext: BrowserContext;
  let page: Page;
  let totalOrder: number = 0;

  // New order by customer data
  const orderByCustomerData: FakerOrder = new FakerOrder({
    customer: dataCustomers.johnDoe,
    products: [
      {
        product: dataProducts.demo_1,
        quantity: 1,
      },
    ],
    paymentMethod: dataPaymentMethods.wirePayment,
  });
  const numberOfCartRules: number = 0;
  // Discount data invalid value
  const discountDataInvalidValue: ProductDiscount = {
    name: 'Test discount',
    type: 'Percent',
    value: '10%',
  };
  // Discount percent superior to 100
  const discountPercentSup100Value: ProductDiscount = {
    name: 'Test discount',
    type: 'Percent',
    value: '500',
  };
  // Discount percent inferior to 0
  const discountPercentInf0Value: ProductDiscount = {
    name: 'Test discount',
    type: 'Percent',
    value: '-2',
  };
  // Discount percent good value
  const discountPercentGoodValue: ProductDiscount = {
    name: 'Test discount percent',
    type: 'Percent',
    value: '50',
  };
  // Discount amount invalid value
  const discountAmountTextValue: ProductDiscount = {
    name: 'Test discount',
    type: 'Amount',
    value: '10 euro',
  };
  // Discount amount negative value
  const discountAmountNegativeValue: ProductDiscount = {
    name: 'Test discount',
    type: 'Amount',
    value: '-10',
  };
  // Discount amount greater than total
  const discountAmountGreaterThanTotal: ProductDiscount = {
    name: 'Test discount',
    type: 'Amount',
    value: '1000',
  };
  // Discount amount good value
  const discountAmountGoodValue: ProductDiscount = {
    name: 'Test discount amount',
    type: 'Amount',
    value: '10.55',
  };
  // Discount amount good value
  const discountFreeShipping: ProductDiscount = {
    name: 'Test discount free shipping',
    type: 'Free shipping',
    value: '',
  };
  const shippingDetailsData: FakerOrderShipping = new FakerOrderShipping({
    trackingNumber: '0523698',
    carrier: dataCarriers.myCarrier.name,
    carrierID: dataCarriers.myCarrier.id,
  });
  const shippingDetailsCost: string = '€8.40';

  // Pre-condition - Create order by default customer
  createOrderByCustomerTest(orderByCustomerData, baseContext);

  // before and after functions
  before(async function () {
    browserContext = await utilsPlaywright.createBrowserContext(this.browser);
    page = await utilsPlaywright.newTab(browserContext);
  });

  after(async () => {
    await utilsPlaywright.closeBrowserContext(browserContext);
  });

  it('should login in BO', async function () {
    await testContext.addContextItem(this, 'testIdentifier', 'loginBO', baseContext);

    await boLoginPage.goTo(page, global.BO.URL);
    await boLoginPage.successLogin(page, global.BO.EMAIL, global.BO.PASSWD);

    const pageTitle = await boDashboardPage.getPageTitle(page);
    expect(pageTitle).to.contains(boDashboardPage.pageTitle);
  });

  // 1 - Go to view order page
  describe('Go to view order page', async () => {
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

    it('should reset all filters', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'resetOrderTable1', baseContext);

      const numberOfOrders = await boOrdersPage.resetAndGetNumberOfLines(page);
      expect(numberOfOrders, 'Number of orders is not correct!').to.be.above(0);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer1', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn, 'Lastname is not correct').to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageProductsBlock1', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle, 'Error when view order page!').to.contains(boOrdersViewBlockProductsPage.pageTitle);
    });

    it('should get the total to pay', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'getTotalToPay', baseContext);

      totalOrder = await boOrdersViewBlockProductsPage.getOrderTotalPrice(page);
      expect(totalOrder).is.not.equal(0);
    });
  });

  // 2 - Create discount
  describe('Create discount and check it', async () => {
    it('should add a discount with invalid value and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountInvalidValue', baseContext);

      const errorMessage = await boOrdersViewBlockProductsPage.addDiscount(page, discountDataInvalidValue);
      expect(errorMessage, 'Error message is not correct!')
        .to.equal(boOrdersViewBlockProductsPage.discountMustBeNumberErrorMessage);
    });

    it('should add a discount percent > 100 and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountPercentSup100', baseContext);

      const errorMessage = await boOrdersViewBlockProductsPage.addDiscount(page, discountPercentSup100Value);
      expect(errorMessage, 'Error message is not correct!')
        .to.equal(boOrdersViewBlockProductsPage.invalidPercentValueErrorMessage);
    });

    it('should add a discount percent < 0 and check the error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountPercentInf0', baseContext);

      const errorMessage = await boOrdersViewBlockProductsPage.addDiscount(page, discountPercentInf0Value);
      expect(errorMessage, 'Error message is not correct!')
        .to.equal(boOrdersViewBlockProductsPage.percentValueNotPositiveErrorMessage);
    });

    it('should add discount percent and check successful message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountPercentGoodValue', baseContext);

      const errorMessage = await boOrdersViewBlockProductsPage.addDiscount(page, discountPercentGoodValue);
      expect(errorMessage, 'Validation message is not correct!')
        .to.equal(boOrdersViewBlockProductsPage.successfulUpdateMessage);
    });

    it('should check the existence of new discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewDiscountTable', baseContext);

      const isVisible = await boOrdersViewBlockProductsPage.isDiscountListTableVisible(page);
      expect(isVisible, 'Discount list table is not visible').to.eq(true);
    });

    it('should check the discount name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountName', baseContext);

      const discountName = await boOrdersViewBlockProductsPage.getTextColumnFromDiscountTable(page, 'name');
      expect(discountName).to.be.equal(discountPercentGoodValue.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue', baseContext);

      const discountValue = await boOrdersViewBlockProductsPage.getTextColumnFromDiscountTable(page, 'value');
      expect(discountValue).to.equal(
        `- €${totalOrder - (totalOrder * parseFloat(discountPercentGoodValue.value)) / 100}`,
      );
    });

    it('should check total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount', baseContext);

      const totalAfterDiscount = await boOrdersViewBlockProductsPage.getOrderTotalPrice(page);
      expect(totalAfterDiscount)
        .to.be.equal(totalOrder - (totalOrder * parseFloat(discountPercentGoodValue.value)) / 100);
    });

    it('should check the total discounts value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalDiscountValue', baseContext);

      const discountValue = await boOrdersViewBlockProductsPage.getOrderTotalDiscounts(page);
      expect(discountValue).to.equal((totalOrder * parseFloat(discountPercentGoodValue.value)) / 100 - totalOrder);
    });

    it('should delete the discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDiscount1', baseContext);

      const validationMessage = await boOrdersViewBlockProductsPage.deleteDiscount(page);
      expect(validationMessage, 'Successful update alert is not correct')
        .to.equal(boOrdersViewBlockProductsPage.successfulUpdateMessage);
    });

    it('should add a discount amount invalid value and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountAmountInvalidValue', baseContext);

      const errorMessage = await boOrdersViewBlockProductsPage.addDiscount(page, discountAmountTextValue);
      expect(errorMessage, 'Error message is not correct!')
        .to.equal(boOrdersViewBlockProductsPage.discountMustBeNumberErrorMessage);
    });

    it('should add a discount amount negative value and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountAmountNegativeValue', baseContext);

      const errorMessage = await boOrdersViewBlockProductsPage.addDiscount(page, discountAmountNegativeValue);
      expect(errorMessage, 'Error message is not correct!').to.equal('Amount value must be greater than 0.');
    });

    it('should add a discount amount greater than the total and check error message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountAmountGreaterThanTotal', baseContext);

      const errorMessage = await boOrdersViewBlockProductsPage.addDiscount(page, discountAmountGreaterThanTotal);
      expect(errorMessage, 'Error message is not correct!')
        .to.equal(boOrdersViewBlockProductsPage.discountCannotExceedTotalErrorMessage);
    });

    it('should add a good discount amount and check validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addCorrectDiscountAmount', baseContext);

      const errorMessage = await boOrdersViewBlockProductsPage.addDiscount(page, discountAmountGoodValue);
      expect(errorMessage, 'Validation message is not correct!')
        .to.equal(boOrdersViewBlockProductsPage.successfulUpdateMessage);
    });

    it('should check the existence of new discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewDiscountTable2', baseContext);

      const isVisible = await boOrdersViewBlockProductsPage.isDiscountListTableVisible(page);
      expect(isVisible, 'Discount list table is not visible').to.eq(true);
    });

    it('should check the discount name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountName2', baseContext);

      const discountName = await boOrdersViewBlockProductsPage.getTextColumnFromDiscountTable(page, 'name');
      expect(discountName).to.be.equal(discountAmountGoodValue.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkDiscountValue2', baseContext);

      const discountValue = await boOrdersViewBlockProductsPage.getTextColumnFromDiscountTable(page, 'value');
      expect(discountValue).to.equal(
        `- €${discountAmountGoodValue.value}`,
      );
    });

    it('should check total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount2', baseContext);

      const totalAfterDiscount = await boOrdersViewBlockProductsPage.getOrderTotalPrice(page);
      expect(totalAfterDiscount).to.be.equal(totalOrder - parseFloat(discountAmountGoodValue.value));
    });

    it('should check the total discounts value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalDiscountValue2', baseContext);

      const discountValue = await boOrdersViewBlockProductsPage.getOrderTotalDiscounts(page);
      expect(discountValue).to.equal(parseFloat(discountAmountGoodValue.value) * -1);
    });
  });

  // 3 - Check cart rules created
  describe('Check created cart rules on \'Catalog > Discount\' page', async () => {
    it('should go to \'Catalog > Discounts\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToDiscountsPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.catalogParentLink,
        boDashboardPage.discountsLink,
      );

      const pageTitle = await boCartRulesPage.getPageTitle(page);
      expect(pageTitle).to.contains(boCartRulesPage.pageTitle);
    });
    [
      {
        args: {
          testIdentifier: 'filterFirstDiscount',
          filterType: 'input',
          filterBy: 'name',
          filterValue: discountAmountGoodValue.name,
        },
      },
      {
        args: {
          testIdentifier: 'filterSecondDiscount',
          filterType: 'input',
          filterBy: 'name',
          filterValue: discountPercentGoodValue.name,
        },
      },
    ].forEach((test) => {
      it(`should filter by ${test.args.filterBy} '${test.args.filterValue}'`, async function () {
        await testContext.addContextItem(this, 'testIdentifier', test.args.testIdentifier, baseContext);

        await boCartRulesPage.filterCartRules(
          page,
          test.args.filterType,
          test.args.filterBy,
          test.args.filterValue,
        );

        const numberOfCartRulesAfterFilter = await boCartRulesPage.getNumberOfElementInGrid(page);
        expect(numberOfCartRulesAfterFilter).to.be.equal(1);

        for (let row = 1; row <= numberOfCartRulesAfterFilter; row++) {
          const textColumn = await boCartRulesPage.getTextColumn(page, row, test.args.filterBy);
          expect(textColumn).to.contains(test.args.filterValue);
        }
      });

      it('should reset all filters', async function () {
        await testContext.addContextItem(this, 'testIdentifier', `${test.args.testIdentifier}Reset`, baseContext);

        const numberOfCartRulesAfterReset = await boCartRulesPage.resetAndGetNumberOfLines(page);
        expect(numberOfCartRulesAfterReset).to.equal(numberOfCartRules + 2);
      });
    });
  });

  // 4 - Go back to view order page
  describe('Go back to view order page and delete discount', async () => {
    it('should go to \'Orders > Orders\' page', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'goToOrdersPage2', baseContext);

      await boDashboardPage.goToSubMenu(
        page,
        boDashboardPage.ordersParentLink,
        boDashboardPage.ordersLink,
      );
      await boOrdersPage.closeSfToolBar(page);

      const pageTitle = await boOrdersPage.getPageTitle(page);
      expect(pageTitle).to.contains(boOrdersPage.pageTitle);
    });

    it(`should filter the Orders table by 'Customer: ${dataCustomers.johnDoe.lastName}'`, async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'filterByCustomer2', baseContext);

      await boOrdersPage.filterOrders(page, 'input', 'customer', dataCustomers.johnDoe.lastName);

      const textColumn = await boOrdersPage.getTextColumn(page, 'customer', 1);
      expect(textColumn, 'Lastname is not correct').to.contains(dataCustomers.johnDoe.lastName);
    });

    it('should view the order', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'orderPageProductsBlock2', baseContext);

      await boOrdersPage.goToOrder(page, 1);

      const pageTitle = await boOrdersViewBlockProductsPage.getPageTitle(page);
      expect(pageTitle, 'Error when view order page!').to.contains(boOrdersViewBlockProductsPage.pageTitle);
    });

    it('should delete the created discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteDiscount2', baseContext);

      const validationMessage = await boOrdersViewBlockProductsPage.deleteDiscount(page);
      expect(validationMessage, 'Successful update alert is not correct')
        .to.equal(boOrdersViewBlockProductsPage.successfulUpdateMessage);
    });
  });

  // 5 - Create discount
  describe('Create free shipping cart rule and check it', async () => {
    it('should add cart rule free shipping and check validation message', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'addDiscountFreeShipping', baseContext);

      const errorMessage = await boOrdersViewBlockProductsPage.addDiscount(page, discountFreeShipping);
      expect(errorMessage, 'Error message is not correct!')
        .to.equal(boOrdersViewBlockProductsPage.successfulUpdateMessage);
    });

    it('should check the existence of new discount table', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFreeShippingDiscountTable', baseContext);

      const isVisible = await boOrdersViewBlockProductsPage.isDiscountListTableVisible(page);
      expect(isVisible, 'Discount list table is not visible').to.eq(true);
    });

    it('should check the discount name', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFreeShippingDiscountName', baseContext);

      const discountName = await boOrdersViewBlockProductsPage.getTextColumnFromDiscountTable(page, 'name');
      expect(discountName).to.be.equal(discountFreeShipping.name);
    });

    it('should check the discount value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkFreeShippingDiscountValue', baseContext);

      const discountValue = await boOrdersViewBlockProductsPage.getTextColumnFromDiscountTable(page, 'value');
      expect(discountValue).to.equal('€0.00');
    });

    it('should click on \'Carriers\' tab', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'displayCarriersTab', baseContext);

      const isTabOpened = await boOrdersViewBlockTabListPage.goToCarriersTab(page);
      expect(isTabOpened).to.eq(true);
    });

    it('should click on \'Edit\' link and check the modal', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'clickOnEditLink', baseContext);

      const isModalVisible = await boOrdersViewBlockTabListPage.clickOnEditLink(page);
      expect(isModalVisible, 'Edit shipping modal is not visible!').to.eq(true);
    });

    it('should update the carrier and add a tracking number', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'updateCarrier', baseContext);

      const textResult = await boOrdersViewBlockTabListPage.setShippingDetails(page, shippingDetailsData);
      expect(textResult).to.equal(boOrdersViewBlockTabListPage.successfulUpdateMessage);
    });

    it('should check that the discount value is changed', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkNewCarrierDiscountValue', baseContext);

      const discountValue = await boOrdersViewBlockProductsPage.getTextColumnFromDiscountTable(page, 'value');
      expect(discountValue).to.equal(`- ${shippingDetailsCost}`);
    });

    it('should check total after discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalAfterDiscount3', baseContext);

      const totalAfterDiscount = await boOrdersViewBlockProductsPage.getOrderTotalPrice(page);
      expect(totalAfterDiscount).to.be.equal(totalOrder);
    });

    it('should check the total discounts value', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'checkTotalDiscountValue3', baseContext);

      const discountValue = await boOrdersViewBlockProductsPage.getOrderTotalDiscounts(page);
      expect(discountValue).to.equal(-8.40);
    });

    it('should delete the created discount', async function () {
      await testContext.addContextItem(this, 'testIdentifier', 'deleteFreeShippingDiscount', baseContext);

      const validationMessage = await boOrdersViewBlockProductsPage.deleteDiscount(page);
      expect(validationMessage, 'Successful update alert is not correct')
        .to.equal(boOrdersViewBlockProductsPage.successfulUpdateMessage);
    });
  });

  // Post-Condition - Bulk delete cart rules
  bulkDeleteCartRuleTest(baseContext);
});
